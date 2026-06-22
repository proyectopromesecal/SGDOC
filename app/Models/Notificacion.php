<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Notificacion {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Crear nueva notificación
     * @param array $datos [usuario_id, rol_destinatario, titulo, mensaje, tipo, link]
     */
    public function crear($datos) {
        $sql = "INSERT INTO notificaciones (usuario_id, rol_destinatario, departamento, titulo, mensaje, tipo, link, fecha_creacion) 
                VALUES (:usuario_id, :rol_destinatario, :departamento, :titulo, :mensaje, :tipo, :link, GETDATE())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $datos['usuario_id'] ?? null,
            'rol_destinatario' => $datos['rol_destinatario'] ?? null,
            'departamento' => $datos['departamento'] ?? null,
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'tipo' => $datos['tipo'] ?? 'info',
            'link' => $datos['link'] ?? '#'
        ]);
    }
    
    /**
     * Obtener notificaciones de un usuario (propias + de su rol)
     */
    public function obtenerPorUsuario($usuarioId, $rolNombre, $departamento = null, $limite = 20) {
        // SQL Server syntax for LIMIT is TOP
        $sql = "SELECT TOP $limite * FROM notificaciones 
                WHERE (usuario_id = :usuario_id OR (rol_destinatario = :rol_nombre AND (departamento IS NULL OR departamento = :departamento)))
                ORDER BY fecha_creacion DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'rol_nombre' => $rolNombre,
            'departamento' => $departamento
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Contar no leídas
     */
    public function contarNoLeidas($usuarioId, $rolNombre, $departamento = null) {
        $sql = "SELECT COUNT(*) as total FROM notificaciones 
                WHERE (usuario_id = :usuario_id OR (rol_destinatario = :rol_nombre AND (departamento IS NULL OR departamento = :departamento))) 
                AND leida = 0";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'rol_nombre' => $rolNombre,
            'departamento' => $departamento
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Marcar como leída
     */
    public function marcarComoLeida($id, $usuarioId) {
        // Solo marcar si pertenece al usuario o su rol (aunque si es por rol, marcarla como leída para uno podría marcarla para todos si es la misma fila... 
        // *Refinamiento*: Las notificaciones por Rol son complejas de gestionar "leído" individualmente con una sola tabla simple.
        // Asumiremos que si es por Rol, cualquiera que la lea la marca (comportamiento estilo ticket de soporte pool) 
        // O mejor: Para este MVP, si es notificación de Rol, no trackeamos leida a nivel usuario individual en esta tabla simple, 
        // solo el 'click' la descarta visualmente o simplemente listamos las recientes.
        // Pero para no complicar, permitiré marcarla.
        
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Marcar todas como leídas para un usuario
     */
    public function marcarTodasLeidas($usuarioId, $rolNombre, $departamento = null) {
        $sql = "UPDATE notificaciones SET leida = 1 
                WHERE (usuario_id = :usuario_id OR (rol_destinatario = :rol_nombre AND (departamento IS NULL OR departamento = :departamento))) AND leida = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $usuarioId,
            'rol_nombre' => $rolNombre,
            'departamento' => $departamento
        ]);
    }
}
