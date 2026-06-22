<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class TipoSolicitud {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los tipos de solicitud
     */
    public function obtenerTodos($pagina = 0, $limite = 10) {
        $sql = "SELECT * FROM tipos_solicitudes ORDER BY nombre ASC";
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limite ROWS ONLY";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarTodos() {
        $sql = "SELECT COUNT(*) FROM tipos_solicitudes";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * Obtener tipos de solicitud activos
     */
    public function obtenerActivos() {
        $sql = "SELECT * FROM tipos_solicitudes WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener tipo de solicitud por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM tipos_solicitudes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crear nuevo tipo de solicitud
     */
    public function crear($datos) {
        $sql = "INSERT INTO tipos_solicitudes (nombre, descripcion, activo) 
                VALUES (:nombre, :descripcion, :activo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'activo' => $datos['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualizar tipo de solicitud
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE tipos_solicitudes 
                SET nombre = :nombre, descripcion = :descripcion 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null
        ]);
    }
    
    /**
     * Cambiar estado del tipo de solicitud (activo/inactivo)
     */
    public function cambiarEstado($id, $activo) {
        $sql = "UPDATE tipos_solicitudes SET activo = :activo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'activo' => $activo
        ]);
    }

    /**
     * Verificar si existe un tipo de solicitud con el mismo nombre
     */
    public function existeNombre($nombre, $exceptId = null) {
        $sql = "SELECT COUNT(*) FROM tipos_solicitudes WHERE nombre = :nombre";
        $params = ['nombre' => $nombre];
        
        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params['except_id'] = $exceptId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
