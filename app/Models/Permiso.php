<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Permiso {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los permisos agrupados por módulo
     */
    public function obtenerTodosAgrupados() {
        $permisos = $this->obtenerTodosPlanos();
        
        $agrupados = [];
        foreach ($permisos as $p) {
            $modulo = !empty($p['modulo']) ? $p['modulo'] : 'Sistema';
            $agrupados[$modulo][] = $p;
        }
        return $agrupados;
    }

    /**
     * Obtener todos los permisos sin agrupar
     */
    public function obtenerTodosPlanos() {
        $sql = "SELECT * FROM permisos ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener IDs de permisos asignados a un rol
     */
    public function obtenerIdsPorRol($rol_id) {
        $sql = "SELECT permiso_id FROM rol_permisos WHERE rol_id = :rol_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rol_id' => $rol_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Actualizar los permisos de un rol
     */
    public function actualizarPermisosRol($rol_id, $permisos_ids) {
        try {
            $this->db->beginTransaction();
            
            // Eliminar permisos actuales
            $stmtDel = $this->db->prepare("DELETE FROM rol_permisos WHERE rol_id = :rol_id");
            $stmtDel->execute(['rol_id' => $rol_id]);
            
            // Insertar nuevos permisos
            if (!empty($permisos_ids)) {
                $sqlIns = "INSERT INTO rol_permisos (rol_id, permiso_id) VALUES (:rol_id, :permiso_id)";
                $stmtIns = $this->db->prepare($sqlIns);
                foreach ($permisos_ids as $p_id) {
                    $stmtIns->execute(['rol_id' => $rol_id, 'permiso_id' => $p_id]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
