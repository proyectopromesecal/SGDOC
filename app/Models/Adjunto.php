<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Adjunto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Registrar un archivo adjunto
     */
    public function registrar($documentoId, $nombreArchivo) {
        $sql = "INSERT INTO documentos_adjuntos (documento_id, nombre_archivo) 
                VALUES (:documento_id, :nombre_archivo)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'documento_id' => $documentoId,
            'nombre_archivo' => $nombreArchivo
        ]);
    }
    
    /**
     * Obtener adjuntos de un documento
     */
    public function obtenerPorDocumento($documentoId) {
        $sql = "SELECT * FROM documentos_adjuntos WHERE documento_id = :documento_id ORDER BY fecha_creacion ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['documento_id' => $documentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener adjunto por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM documentos_adjuntos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
