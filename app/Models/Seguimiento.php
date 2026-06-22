<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Seguimiento {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Registrar un hito en el seguimiento del documento
     */
    public function registrar($documento_id, $usuario_id, $estado_anterior, $estado_nuevo, $accion, $detalles = '') {
        $sql = "INSERT INTO seguimiento_documentos 
                (documento_id, usuario_id, estado_anterior, estado_nuevo, accion, detalles) 
                VALUES (:documento_id, :usuario_id, :estado_anterior, :estado_nuevo, :accion, :detalles)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'documento_id' => $documento_id,
            'usuario_id' => $usuario_id,
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => $estado_nuevo,
            'accion' => $accion,
            'detalles' => $detalles
        ]);
    }
    
    /**
     * Obtener el historial completo de un documento
     */
    public function obtenerPorDocumento($documento_id) {
        $sql = "SELECT s.*, u.usuario as nombre_usuario, u.departamento, u.cargo
                FROM seguimiento_documentos s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                WHERE s.documento_id = :documento_id
                ORDER BY s.fecha_movimiento ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['documento_id' => $documento_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener los movimientos más recientes (todas las trazas)
     */
    public function obtenerRecientesPaginated($pagina = 1, $limite = 150) {
        $sql = "SELECT s.*, u.usuario as nombre_usuario, u.rol_id, r.nombre as rol_nombre, u.departamento, d.descripcion as doc_descripcion
                FROM seguimiento_documentos s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                INNER JOIN roles r ON u.rol_id = r.id
                INNER JOIN documentos d ON s.documento_id = d.id
                ORDER BY s.fecha_movimiento DESC";
        
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limite ROWS ONLY";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarTodos() {
        $sql = "SELECT COUNT(*) FROM seguimiento_documentos";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}
