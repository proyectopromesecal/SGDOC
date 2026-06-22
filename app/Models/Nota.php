<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Nota {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function obtenerTodas($pagina = 0, $limite = 10) {
        $sql = "SELECT n.*, u.usuario as autor_nombre 
                FROM notas_proyecto n 
                INNER JOIN usuarios u ON n.autor_id = u.id 
                ORDER BY n.fecha_creacion DESC";
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limite ROWS ONLY";
        }
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarTodas() {
        $sql = "SELECT COUNT(*) FROM notas_proyecto";
        return $this->db->query($sql)->fetchColumn();
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO notas_proyecto (titulo, contenido, autor_id, color_tag) 
                VALUES (:titulo, :contenido, :autor_id, :color_tag)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'titulo' => $datos['titulo'],
            'contenido' => $datos['contenido'],
            'autor_id' => $datos['autor_id'],
            'color_tag' => $datos['color_tag'] ?? '#007281'
        ]);
    }
}
