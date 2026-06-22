<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Bitacora {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Registrar acción en bitácora
     */
    public function registrar($usuarioId, $accion, $detalles = '', $ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $sql = "INSERT INTO bitacora (usuario_id, accion, detalles, ip) 
                VALUES (:usuario_id, :accion, :detalles, :ip)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'detalles' => $detalles,
            'ip' => $ip
        ]);
    }
    
    /**
     * Obtener registros de bitácora
     */
    public function obtenerRegistros($filtros = [], $pagina = 1, $limite = 100) {
        $sql = "SELECT b.*, u.usuario as nombre_usuario 
                FROM bitacora b 
                INNER JOIN usuarios u ON b.usuario_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND b.usuario_id = :usuario_id";
            $params['usuario_id'] = $filtros['usuario_id'];
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND b.fecha >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND b.fecha <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['accion'])) {
            $sql .= " AND b.accion LIKE :accion";
            $params['accion'] = '%' . $filtros['accion'] . '%';
        }
        
        $sql .= " ORDER BY b.fecha DESC";
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT :limite ROWS ONLY";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if ($pagina > 0) {
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarRegistros($filtros = []) {
        $sql = "SELECT COUNT(*) FROM bitacora b WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND b.usuario_id = :usuario_id";
            $params['usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND b.fecha >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND b.fecha <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        if (!empty($filtros['accion'])) {
            $sql .= " AND b.accion LIKE :accion";
            $params['accion'] = '%' . $filtros['accion'] . '%';
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
