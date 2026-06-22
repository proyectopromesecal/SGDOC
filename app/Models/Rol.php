<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Rol {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los roles
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM roles ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un rol por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo rol
     */
    public function crear($nombre) {
        $sql = "INSERT INTO roles (nombre) VALUES (:nombre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['nombre' => trim($nombre)]);
    }

    /**
     * Actualizar el nombre de un rol
     */
    public function actualizar($id, $nombre) {
        $sql = "UPDATE roles SET nombre = :nombre WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => trim($nombre),
            'id' => $id
        ]);
    }

    /**
     * Eliminar un rol, verificando que no esté en uso y no sea Administrador
     */
    public function eliminar($id) {
        // Verificar si es el rol Administrador (id 1 por lo general, o por nombre)
        $rol = $this->obtenerPorId($id);
        if (!$rol || strtolower($rol['nombre']) === 'administrador') {
            return false; // No se puede eliminar al Administrador
        }

        // Verificar si hay usuarios con este rol asignado
        $sqlCheck = "SELECT COUNT(*) FROM usuarios WHERE rol_id = :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute(['id' => $id]);
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            return false; // Hay usuarios usando este rol
        }

        // Eliminar permisos asociados a este rol primero
        $sqlPermisos = "DELETE FROM permisos_roles WHERE rol_id = :id";
        $stmtPermisos = $this->db->prepare($sqlPermisos);
        $stmtPermisos->execute(['id' => $id]);

        // Finalmente, eliminar el rol
        $sql = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
