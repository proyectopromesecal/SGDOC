<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Configuracion {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todas las configuraciones
     */
    public function obtenerTodas() {
        $sql = "SELECT clave, valor FROM configuracion";
        $stmt = $this->db->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = [];
        foreach ($resultados as $row) {
            $config[$row['clave']] = $row['valor'];
        }
        return $config;
    }
    
    /**
     * Guardar una configuración
     */
    public function guardar($clave, $valor) {
        $sql = "UPDATE configuracion SET valor = :valor WHERE clave = :clave";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'clave' => $clave,
            'valor' => $valor
        ]);
    }
    
    /**
     * Guardar múltiples configuraciones
     */
    public function guardarMuchas($datos) {
        $success = true;
        foreach ($datos as $clave => $valor) {
            if (!$this->guardar($clave, $valor)) {
                $success = false;
            }
        }
        return $success;
    }
}
