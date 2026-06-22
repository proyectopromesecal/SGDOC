<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Documento {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Crear nuevo documento
     */
    public function crear($datos) {
        // Verificar si la columna departamento_origen_id existe (puede no haber sido migrada aún)
        $sql = "INSERT INTO documentos (id, descripcion, tipo, ruta_original, usuario_id, es_digitalizado, estado, prioridad, tipo_solicitud_id, departamento_destino_id, departamento_origen_id) 
                VALUES (:id, :descripcion, :tipo, :ruta_original, :usuario_id, :es_digitalizado, :estado, :prioridad, :tipo_solicitud_id, :departamento_destino_id, :departamento_origen_id)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $datos['id'],
                'descripcion' => $datos['descripcion'],
                'tipo' => $datos['tipo'],
                'ruta_original' => $datos['ruta_original'],
                'usuario_id' => $datos['usuario_id'],
                'es_digitalizado' => $datos['es_digitalizado'] ?? 0,
                'estado' => $datos['estado'] ?? 'SOLICITADO',
                'prioridad' => $datos['prioridad'] ?? 'Normal',
                'tipo_solicitud_id' => $datos['tipo_solicitud_id'] ?? null,
                'departamento_destino_id' => $datos['departamento_destino_id'] ?? null,
                'departamento_origen_id' => $datos['departamento_origen_id'] ?? null
            ]);
        } catch (\PDOException $e) {
            // Fallback: si departamento_origen_id no existe en la tabla, insertar sin ese campo
            if (strpos($e->getMessage(), 'departamento_origen_id') !== false) {
                $sql2 = "INSERT INTO documentos (id, descripcion, tipo, ruta_original, usuario_id, es_digitalizado, estado, prioridad, tipo_solicitud_id, departamento_destino_id) 
                         VALUES (:id, :descripcion, :tipo, :ruta_original, :usuario_id, :es_digitalizado, :estado, :prioridad, :tipo_solicitud_id, :departamento_destino_id)";
                $stmt2 = $this->db->prepare($sql2);
                return $stmt2->execute([
                    'id' => $datos['id'],
                    'descripcion' => $datos['descripcion'],
                    'tipo' => $datos['tipo'],
                    'ruta_original' => $datos['ruta_original'],
                    'usuario_id' => $datos['usuario_id'],
                    'es_digitalizado' => $datos['es_digitalizado'] ?? 0,
                    'estado' => $datos['estado'] ?? 'SOLICITADO',
                    'prioridad' => $datos['prioridad'] ?? 'Normal',
                    'tipo_solicitud_id' => $datos['tipo_solicitud_id'] ?? null,
                    'departamento_destino_id' => $datos['departamento_destino_id'] ?? null
                ]);
            }
            throw $e;
        }
    }
    
    /**
     * Obtener todos los documentos
     */
    public function obtenerTodos($filtros = [], $pagina = 1, $limite = 10) {
        $sql = "SELECT d.*, u.usuario as nombre_usuario,
                       dept_dest.nombre as depto_destino_nombre,
                       dept_orig.nombre as depto_origen_nombre,
                       ts.nombre as tipo_solicitud_nombre
                FROM documentos d 
                INNER JOIN usuarios u ON d.usuario_id = u.id 
                LEFT JOIN departamentos dept_dest ON d.departamento_destino_id = dept_dest.id
                LEFT JOIN departamentos dept_orig ON d.departamento_origen_id = dept_orig.id
                LEFT JOIN tipos_solicitudes ts ON d.tipo_solicitud_id = ts.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND d.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND d.usuario_id = :usuario_id";
            $params['usuario_id'] = $filtros['usuario_id'];
        }
        
        // Encargado de departamento: Ve SOLICITADOS de su departamento ORIGEN
        if (!empty($filtros['departamento_origen_id'])) {
            $sql .= " AND (d.departamento_origen_id = :dep_origen_id OR (d.departamento_origen_id IS NULL AND u.departamento = :dep_origen_nombre)) AND d.estado = 'SOLICITADO'";
            $params['dep_origen_id'] = $filtros['departamento_origen_id'];
            $params['dep_origen_nombre'] = $filtros['departamento_encargado_origen'] ?? 'N/A';
        } elseif (!empty($filtros['departamento_encargado_origen'])) {
            $sql .= " AND u.departamento = :dep_encargado AND d.estado = 'SOLICITADO'";
            $params['dep_encargado'] = $filtros['departamento_encargado_origen'];
        }

        // Jefe de departamento: Ve AUTORIZADO_ENCARGADO dirigidos a su departamento DESTINO
        if (!empty($filtros['departamento_destino_id_jefe'])) {
            $sql .= " AND (d.departamento_destino_id = :dep_destino_jefe OR (d.departamento_destino_id IS NULL AND u.departamento = :dep_destino_jefe_nombre)) AND d.estado = 'AUTORIZADO_ENCARGADO'";
            $params['dep_destino_jefe'] = $filtros['departamento_destino_id_jefe'];
            $params['dep_destino_jefe_nombre'] = $filtros['departamento_encargado'] ?? 'N/A';
        } elseif (!empty($filtros['departamento_encargado'])) {
            $sql .= " AND COALESCE(dept_dest.nombre, u.departamento) = :departamento AND d.estado = 'AUTORIZADO_ENCARGADO'";
            $params['departamento'] = $filtros['departamento_encargado'];
        }
        
        if (!empty($filtros['tipo'])) {
            $sql .= " AND d.tipo = :tipo";
            $params['tipo'] = $filtros['tipo'];
        }
        
        $sql .= " ORDER BY d.fecha_creacion DESC";
        
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limite ROWS ONLY";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de documentos bajo ciertos filtros
     */
    public function contarTodos($filtros = []) {
        $sql = "SELECT COUNT(*) FROM documentos d 
                INNER JOIN usuarios u ON d.usuario_id = u.id 
                LEFT JOIN departamentos dept_dest ON d.departamento_destino_id = dept_dest.id
                LEFT JOIN departamentos dept_orig ON d.departamento_origen_id = dept_orig.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND d.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND d.usuario_id = :usuario_id";
            $params['usuario_id'] = $filtros['usuario_id'];
        }

        // Encargado de departamento: SOLICITADOS de su departamento ORIGEN
        if (!empty($filtros['departamento_origen_id'])) {
            $sql .= " AND (d.departamento_origen_id = :dep_origen_id OR (d.departamento_origen_id IS NULL AND u.departamento = :dep_origen_nombre)) AND d.estado = 'SOLICITADO'";
            $params['dep_origen_id'] = $filtros['departamento_origen_id'];
            $params['dep_origen_nombre'] = $filtros['departamento_encargado_origen'] ?? 'N/A';
        } elseif (!empty($filtros['departamento_encargado_origen'])) {
            $sql .= " AND u.departamento = :dep_encargado AND d.estado = 'SOLICITADO'";
            $params['dep_encargado'] = $filtros['departamento_encargado_origen'];
        }

        // Jefe de departamento: AUTORIZADO_ENCARGADO dirigidos a su departamento DESTINO
        if (!empty($filtros['departamento_destino_id_jefe'])) {
            $sql .= " AND (d.departamento_destino_id = :dep_destino_jefe OR (d.departamento_destino_id IS NULL AND u.departamento = :dep_destino_jefe_nombre)) AND d.estado = 'AUTORIZADO_ENCARGADO'";
            $params['dep_destino_jefe'] = $filtros['departamento_destino_id_jefe'];
            $params['dep_destino_jefe_nombre'] = $filtros['departamento_encargado'] ?? 'N/A';
        } elseif (!empty($filtros['departamento_encargado'])) {
            $sql .= " AND COALESCE(dept_dest.nombre, u.departamento) = :departamento AND d.estado = 'AUTORIZADO_ENCARGADO'";
            $params['departamento'] = $filtros['departamento_encargado'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    
    /**
     * Obtener documento por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT d.*, u.usuario as nombre_usuario, u.departamento as depto_solicitante,
                       dept_dest.nombre as depto_destino_nombre,
                       dept_orig.nombre as depto_origen_nombre,
                       ts.nombre as tipo_solicitud_nombre
                FROM documentos d 
                INNER JOIN usuarios u ON d.usuario_id = u.id 
                LEFT JOIN departamentos dept_dest ON d.departamento_destino_id = dept_dest.id
                LEFT JOIN departamentos dept_orig ON d.departamento_origen_id = dept_orig.id
                LEFT JOIN tipos_solicitudes ts ON d.tipo_solicitud_id = ts.id
                WHERE d.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function actualizarEstado($id, $estado, $rutaFirmado = null) {
        $sql = "UPDATE documentos SET estado = :estado";
        $params = ['id' => $id, 'estado' => $estado];
        
        if ($rutaFirmado) {
            $sql .= ", ruta_firmado = :ruta_firmado";
            $params['ruta_firmado'] = $rutaFirmado;
        }

        // Si es un estado final, marcar fecha de finalización
        if (in_array($estado, ['AUTORIZADO', 'DIGITALIZADO'])) {
            $sql .= ", fecha_finalizacion = GETDATE()";
        } else {
            $sql .= ", fecha_finalizacion = NULL";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Rechazar documento con comentario
     */
    public function rechazarConComentario($id, $comentario) {
        $sql = "UPDATE documentos SET estado = 'RECHAZADO', comentario_rechazo = :comentario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'comentario' => $comentario]);
    }

    /**
     * Agregar observación sin cambiar estado (para Gerencia)
     */
    public function agregarObservacion($id, $comentario) {
        $sql = "UPDATE documentos SET comentario_rechazo = :comentario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'comentario' => $comentario]);
    }

    /**
     * Devolver a estado SOLICITADO con comentario (para Compras)
     */
    public function devolverASolicitado($id, $comentario) {
        $sql = "UPDATE documentos SET estado = 'SOLICITADO', comentario_rechazo = :comentario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'comentario' => $comentario]);
    }

    /**
     * Actualizar documento existente (para correcciones del solicitante)
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE documentos SET 
                descripcion = :descripcion, 
                tipo = :tipo, 
                ruta_original = :ruta_original, 
                prioridad = :prioridad,
                estado = 'SOLICITADO', 
                comentario_rechazo = NULL,
                tipo_solicitud_id = :tipo_solicitud_id,
                departamento_destino_id = :departamento_destino_id
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'descripcion' => $datos['descripcion'],
            'tipo' => $datos['tipo'],
            'ruta_original' => $datos['ruta_original'],
            'prioridad' => $datos['prioridad'] ?? 'Normal',
            'tipo_solicitud_id' => $datos['tipo_solicitud_id'] ?? null,
            'departamento_destino_id' => $datos['departamento_destino_id'] ?? null
        ]);
    }
    
    /**
     * Obtener estadísticas de documentos
     */
    public function obtenerEstadisticas($usuarioId = null) {
        $sql = "SELECT 
                    estado,
                    COUNT(*) as total
                FROM documentos";
        
        $params = [];
        if ($usuarioId) {
            $sql .= " WHERE usuario_id = :usuario_id";
            $params['usuario_id'] = $usuarioId;
        }
        
        $sql .= " GROUP BY estado";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si el ID ya existe
     */
    public function existeId($id) {
        $sql = "SELECT COUNT(*) FROM documentos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
