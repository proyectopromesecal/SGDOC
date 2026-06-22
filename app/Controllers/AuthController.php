<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Bitacora;
use App\Services\LdapService;

class AuthController {
    private $usuarioModel;
    private $bitacoraModel;
    private $ldapService;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->bitacoraModel = new Bitacora();
        $this->ldapService = new LdapService();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        require_once __DIR__ . '/../../views/auth/login.php';
    }
    
    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }
        
        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($usuario) || empty($password)) {
            $_SESSION['error'] = 'Usuario y contraseña son requeridos';
            header('Location: /login');
            exit;
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $intentos = $this->usuarioModel->obtenerIntentosFallidos($ip, $usuario);

        if ($intentos && $intentos['intentos'] >= 5) {
            $ultimo = strtotime($intentos['ultimo_intento']);
            $espera = 15 * 60; // 15 minutos
            
            if (time() - $ultimo < $espera) {
                $_SESSION['error'] = 'Demasiados intentos fallidos. Por favor, espere 15 minutos.';
                header('Location: /login');
                exit;
            }
        }
        
        // 1. Intentar Autenticación LDAP
        $ldapUser = $this->ldapService->autenticar($usuario, $password);
        $user = null;

        if ($ldapUser) {
            // El usuario autenticó correctamente en el AD
            $_SESSION['ldap_user'] = $usuario;
            $_SESSION['ldap_pass'] = $password;
            
            // Verificamos si existe en nuestra base de datos local
            $user = $this->usuarioModel->obtenerPorUsuario($usuario);

            if (!$user) {
                // Si no existe localmente, lo creamos (Shadow User)
                // Usamos el rol 6 (Pendiente de Acceso) por defecto
                
                // Mapear el departamento de LDAP al catálogo de la base de datos
                $departamentoTexto = $ldapUser['departamento'] ?? '';
                $departamentoId = null;
                if (!empty($departamentoTexto)) {
                    $db = \App\Core\Database::getInstance()->getConnection();
                    $stmtDept = $db->prepare("SELECT id FROM departamentos WHERE nombre = :nombre");
                    $stmtDept->execute(['nombre' => $departamentoTexto]);
                    $deptRow = $stmtDept->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($deptRow) {
                        $departamentoId = $deptRow['id'];
                    } else {
                        // Crear el departamento dinámicamente si no existe en el catálogo
                        $stmtInsertDept = $db->prepare("INSERT INTO departamentos (nombre, activo) VALUES (:nombre, 1)");
                        $stmtInsertDept->execute(['nombre' => $departamentoTexto]);
                        $departamentoId = $db->lastInsertId();
                    }
                }

                $datosNuevo = [
                    'usuario' => $usuario,
                    'password' => bin2hex(random_bytes(16)), // Password aleatorio, no se usará
                    'rol_id' => 6, // Pendiente de Acceso
                    'nombre' => $ldapUser['nombre'] ?? $usuario,
                    'email' => $ldapUser['email'] ?? '',
                    'departamento' => $departamentoTexto,
                    'cargo' => $ldapUser['cargo'] ?? '',
                    'tipo_auth' => 'LDAP',
                    'departamento_id' => $departamentoId
                ];
                
                $this->usuarioModel->crear($datosNuevo);
                $user = $this->usuarioModel->obtenerPorUsuario($usuario);
            }
        } else {
            // 2. Si LDAP falla o no está disponible, intentar Autenticación Local
            $user = $this->usuarioModel->autenticar($usuario, $password);
        }
        
        if ($user) {
            $this->usuarioModel->limpiarIntentosFallidos($ip, $usuario);
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol_id'] = $user['rol_id'];
            $_SESSION['rol_nombre'] = $user['rol_nombre'];
            $_SESSION['departamento_id'] = $user['departamento_id'] ?? null;
            $_SESSION['departamento'] = $user['departamento_nombre'] ?? $user['departamento'] ?? '';
            $_SESSION['permisos'] = $this->usuarioModel->obtenerPermisos($user['rol_id']);
            
            $this->bitacoraModel->registrar(
                $user['id'],
                'LOGIN',
                'Inicio de sesión exitoso' . ($ldapUser ? ' (LDAP)' : ' (Local)')
            );
            
            // Redirección basada en el rol
            if ($user['rol_id'] == 6) {
                header('Location: /solicitud-acceso');
            } else {
                header('Location: /dashboard');
            }
            exit;
        } else {
            $this->usuarioModel->registrarIntentoFallido($ip, $usuario);
            $_SESSION['error'] = 'Credenciales inválidas';
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        if (isset($_SESSION['usuario_id'])) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'LOGOUT',
                'Cierre de sesión'
            );
        }
        
        session_destroy();
        header('Location: /login');
        exit;
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public static function verificarAutenticacion() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        // Si el usuario está pendiente de acceso, forzar redirección
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($_SESSION['rol_id'] == 6 && $currentPath !== '/solicitud-acceso') {
            header('Location: /solicitud-acceso');
            exit;
        }

        // Si ya tiene acceso y trata de entrar a la solicitud, mover al dashboard
        if ($_SESSION['rol_id'] != 6 && $currentPath === '/solicitud-acceso') {
            header('Location: /dashboard');
            exit;
        }
    }
    
    /**
     * Verificar si el usuario tiene un permiso específico.
     * El rol 'Administrador' (SuperAdmin) siempre tiene acceso total.
     */
    public static function tienePermiso($permiso) {
        // SuperAdmin bypass: el Administrador y Super Administrador siempre tienen todos los permisos
        if (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false) {
            return true;
        }

        if (!isset($_SESSION['permisos'])) {
            // Si el usuario está logueado pero no tiene permisos cargados (sesión antigua)
            if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol_id'])) {
                $usuarioModel = new \App\Models\Usuario();
                $_SESSION['permisos'] = $usuarioModel->obtenerPermisos($_SESSION['rol_id']);
            } else {
                return false;
            }
        }
        return in_array($permiso, $_SESSION['permisos']);
    }

    /**
     * Forzar validación de un permiso
     */
    public static function verificarPermiso($permiso) {
        self::verificarAutenticacion();
        if (!self::tienePermiso($permiso)) {
            http_response_code(403);
            if (file_exists(__DIR__ . '/../../views/errors/403.php')) {
                require_once __DIR__ . '/../../views/errors/403.php';
            } else {
                $_SESSION['error'] = 'No tiene permisos para realizar esta acción: ' . $permiso;
                header('Location: /dashboard');
            }
            exit;
        }
    }

    /**
     * Verificar si el usuario tiene el rol requerido.
     * El rol 'Administrador' (SuperAdmin) siempre pasa esta validación.
     */
    public static function verificarRol($rolesPermitidos) {
        self::verificarAutenticacion();

        // SuperAdmin bypass: el Administrador siempre tiene acceso
        if (strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false) {
            return;
        }
        
        if (!in_array($_SESSION['rol_nombre'], $rolesPermitidos)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            header('Location: /dashboard');
            exit;
        }
    }
    /**
     * Mostrar página de solicitud de acceso
     */
    public function mostrarSolicitudAcceso() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
        
        if ($_SESSION['rol_id'] != 6) {
            header('Location: /dashboard');
            exit;
        }

        $user = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
        require_once __DIR__ . '/../../views/auth/request_access.php';
    }

    /**
     * Procesar el registro de la solicitud (El envío real se hace vía JS con mailto)
     */
    public function procesarSolicitudAcceso() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /solicitud-acceso');
            exit;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $motivo = trim($_POST['motivo'] ?? '');
        
        // Registrar en Bitácora que el usuario envió la solicitud
        $this->bitacoraModel->registrar(
            $usuarioId,
            'SOLICITUD_ACCESO',
            'Solicitud de acceso enviada vía cliente de correo: ' . $motivo
        );

        $_SESSION['success'] = 'Su solicitud ha sido registrada en el sistema. Asegúrese de haber enviado el correo que se abrió en su equipo.';

        header('Location: /solicitud-acceso');
        exit;
    }
}

