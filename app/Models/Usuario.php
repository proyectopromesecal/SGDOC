<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($usuario, $password) {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                WHERE u.usuario = :usuario AND u.status = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    /**
     * Obtener usuario por su nombre de usuario (string)
     */
    public function obtenerPorUsuario($usuario) {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                WHERE u.usuario = :usuario";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener permisos por ID de rol
     */
    public function obtenerPermisos($rol_id) {
        $sql = "SELECT p.nombre 
                FROM permisos p 
                INNER JOIN rol_permisos rp ON p.id = rp.permiso_id 
                WHERE rp.rol_id = :rol_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['rol_id' => $rol_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    
    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (usuario, password, rol_id, firma_digital, nombre, email, departamento, cargo, tipo_auth, departamento_id) 
                VALUES (:usuario, :password, :rol_id, :firma_digital, :nombre, :email, :departamento, :cargo, :tipo_auth, :departamento_id)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'usuario' => $datos['usuario'],
            'password' => password_hash($datos['password'], PASSWORD_BCRYPT),
            'rol_id' => $datos['rol_id'],
            'firma_digital' => $datos['firma_digital'] ?? null,
            'nombre' => $datos['nombre'] ?? null,
            'email' => $datos['email'] ?? null,
            'departamento' => $datos['departamento'] ?? null,
            'cargo' => $datos['cargo'] ?? null,
            'tipo_auth' => $datos['tipo_auth'] ?? 'LOCAL',
            'departamento_id' => $datos['departamento_id'] ?? null
        ]);
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                ORDER BY u.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuariosNormales($pagina = 1, $limite = 10) {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                WHERE u.rol_id != 6
                ORDER BY u.id DESC";
        
        if ($pagina > 0) {
            $offset = ($pagina - 1) * $limite;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limite ROWS ONLY";
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarUsuariosNormales() {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE rol_id != 6";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function obtenerUsuariosPendientes() {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                WHERE u.rol_id = 6
                ORDER BY u.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT u.*, r.nombre as rol_nombre, d.nombre as departamento_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                LEFT JOIN departamentos d ON u.departamento_id = d.id
                WHERE u.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE usuarios SET rol_id = :rol_id";
        $params = [
            'id' => $id,
            'rol_id' => $datos['rol_id']
        ];
        
        // Solo actualizar el nombre de usuario si se proporciona y no es nulo
        if (!empty($datos['usuario'])) {
            $sql .= ", usuario = :usuario";
            $params['usuario'] = $datos['usuario'];
        }
        
        if (!empty($datos['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        if (isset($datos['firma_digital'])) {
            $sql .= ", firma_digital = :firma_digital";
            $params['firma_digital'] = $datos['firma_digital'];
        }

        if (isset($datos['nombre'])) {
            $sql .= ", nombre = :nombre";
            $params['nombre'] = $datos['nombre'];
        }

        if (isset($datos['email'])) {
            $sql .= ", email = :email";
            $params['email'] = $datos['email'];
        }

        if (isset($datos['departamento'])) {
            $sql .= ", departamento = :departamento";
            $params['departamento'] = $datos['departamento'];
        }

        if (isset($datos['cargo'])) {
            $sql .= ", cargo = :cargo";
            $params['cargo'] = $datos['cargo'];
        }

        if (isset($datos['departamento_id'])) {
            $sql .= ", departamento_id = :departamento_id";
            $params['departamento_id'] = $datos['departamento_id'] ?: null;
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado($id, $status) {
        $sql = "UPDATE usuarios SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public function desactivarActivos() {
        $sql = "UPDATE usuarios SET status = 0 WHERE status = 1 AND id != 1 AND rol_id != 6";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
    /**
     * Obtener intentos fallidos por IP y Usuario
     */
    public function obtenerIntentosFallidos($ip, $usuario) {
        $sql = "SELECT intentos, ultimo_intento FROM login_attempts WHERE ip_address = :ip AND usuario = :usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ip' => $ip, 'usuario' => $usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registrar un intento fallido o incrementar el contador
     */
    public function registrarIntentoFallido($ip, $usuario) {
        $sql = "IF EXISTS (SELECT 1 FROM login_attempts WHERE ip_address = :ip AND usuario = :usuario)
                    UPDATE login_attempts SET intentos = intentos + 1, ultimo_intento = GETDATE()
                    WHERE ip_address = :ip AND usuario = :usuario
                ELSE
                    INSERT INTO login_attempts (ip_address, usuario, intentos, ultimo_intento)
                    VALUES (:ip, :usuario, 1, GETDATE())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['ip' => $ip, 'usuario' => $usuario]);
    }

    /**
     * Limpiar intentos fallidos tras login exitoso
     */
    public function limpiarIntentosFallidos($ip, $usuario) {
        $sql = "DELETE FROM login_attempts WHERE ip_address = :ip AND usuario = :usuario";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['ip' => $ip, 'usuario' => $usuario]);
    }
}
