<?php
/**
 * SIGEDOC - Front Controller
 */

if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Iniciar sesión
session_start();

require_once __DIR__ . '/../config.php';

// Global Error Handler & Logging
if (!is_dir(__DIR__ . '/../logs')) mkdir(__DIR__ . '/../logs', 0777, true);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return false;
    $logMsg = "[".date('Y-m-d H:i:s')."] Error ($errno): $errstr in $errfile:$errline\n";
    error_log($logMsg, 3, __DIR__ . '/../logs/app.log');
    return true; // No mostrar por pantalla
});

set_exception_handler(function($e) {
    $logMsg = "[".date('Y-m-d H:i:s')."] Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    error_log($logMsg, 3, __DIR__ . '/../logs/app.log');
    http_response_code(500);
    if (file_exists(__DIR__ . '/../views/errors/500.php')) {
        require_once __DIR__ . '/../views/errors/500.php';
    } else {
        echo "Error Interno del Servidor. Por favor intente más tarde.";
    }
    exit;
});

// Autoloader - Prioritize Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Basic PSR-4 Fallback
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/../app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) return;
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    });
}

// Initialize Components
$router = new App\Core\Router();

// Rutas de Autenticación
$router->add('GET', '/login', 'AuthController', 'mostrarLogin');
$router->add('POST', '/login', 'AuthController', 'login', ['csrf']);
$router->add('GET', '/logout', 'AuthController', 'logout');
$router->add('GET', '/solicitud-acceso', 'AuthController', 'mostrarSolicitudAcceso', ['auth']);
$router->add('POST', '/solicitud-acceso', 'AuthController', 'procesarSolicitudAcceso', ['auth', 'csrf']);

// Rutas de Dashboard
$router->add('GET', '/dashboard', 'DashboardController', 'index', ['auth', 'perm:dashboard_ver']);

// Rutas de Documentos
$router->add('GET', '/documentos', 'DocumentoController', 'listar', ['auth', 'perm:documentos_listar']);
$router->add('GET', '/documentos/crear', 'DocumentoController', 'crear', ['auth', 'perm:documentos_crear']);
$router->add('POST', '/documentos/guardar', 'DocumentoController', 'guardar', ['auth', 'csrf', 'perm:documentos_crear']);
$router->add('GET', '/documentos/digitalizados', 'DocumentoController', 'digitalizados', ['auth', 'perm:archivo_digital_crear']);
$router->add('GET', '/archivo-digital', 'DocumentoController', 'archivoDigital', ['auth', 'perm:archivo_digital_ver']);
$router->add('POST', '/documentos/guardar_digitalizado', 'DocumentoController', 'guardar_digitalizado', ['auth', 'csrf', 'perm:archivo_digital_crear']);
$router->add('GET', '/documentos/ver/{id}', 'DocumentoController', 'ver', ['auth', 'perm:documentos_ver']);
$router->add('GET', '/documentos/editar/{id}', 'DocumentoController', 'editar', ['auth', 'perm:documentos_editar']);
$router->add('POST', '/documentos/actualizar/{id}', 'DocumentoController', 'actualizar', ['auth', 'csrf', 'perm:documentos_editar']);
$router->add('POST', '/documentos/autorizar_encargado/{id}', 'DocumentoController', 'autorizar_encargado', ['auth', 'csrf', 'perm:documentos_autorizar_encargado,administrador']);
$router->add('POST', '/documentos/autorizar_depto/{id}', 'DocumentoController', 'autorizar_depto', ['auth', 'csrf', 'perm:documentos_autorizar_jefe,administrador']);
$router->add('POST', '/documentos/aprobar/{id}', 'DocumentoController', 'aprobar', ['auth', 'csrf', 'perm:documentos_autorizar_jefe,administrador']);
$router->add('POST', '/documentos/autorizar/{id}', 'DocumentoController', 'autorizar', ['auth', 'csrf', 'perm:documentos_autorizar_gerencia,administrador']);
$router->add('POST', '/documentos/rechazar/{id}', 'DocumentoController', 'rechazar', ['auth', 'csrf', 'perm:documentos_autorizar_encargado,documentos_autorizar_jefe,documentos_autorizar_gerencia,administrador']);
$router->add('GET', '/documentos/descargar/{id}/{tipo}', 'DocumentoController', 'descargar', ['auth', 'perm:documentos_ver']);
$router->add('GET', '/documentos/descargar_soporte/{id}', 'DocumentoController', 'descargar_soporte', ['auth', 'perm:documentos_ver']);
$router->add('GET', '/documentos/visualizar_soporte/{id}', 'DocumentoController', 'visualizar_soporte', ['auth', 'perm:documentos_ver']);
$router->add('GET', '/documentos/visualizar/{id}/{tipo}', 'DocumentoController', 'visualizar', ['auth', 'perm:documentos_ver']);

// Rutas de Bitácora
$router->add('GET', '/bitacora', 'BitacoraController', 'index', ['auth', 'perm:bitacora_ver']);

// Rutas de Seguimiento General
$router->add('GET', '/seguimiento', 'SeguimientoController', 'index', ['auth', 'perm:documentos_ver']);

// Rutas de Notas
$router->add('GET', '/notas', 'NotaController', 'index', ['auth']);
$router->add('POST', '/notas/guardar', 'NotaController', 'guardar', ['auth', 'csrf']);

// Rutas de Usuarios
$router->add('GET', '/usuarios', 'UsuarioController', 'index', ['auth', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/guardar', 'UsuarioController', 'guardar', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/actualizar', 'UsuarioController', 'actualizar', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/aprobar/{id}', 'UsuarioController', 'aprobar', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/estado/{id}', 'UsuarioController', 'cambiarEstado', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/desactivar_activos', 'UsuarioController', 'desactivarActivos', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/buscar_ldap', 'UsuarioController', 'buscarLdap', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/activar_ldap', 'UsuarioController', 'activarLdap', ['auth', 'csrf', 'perm:usuarios_gestionar']);
$router->add('POST', '/usuarios/sincronizar_ldap', 'UsuarioController', 'sincronizarLdap', ['auth', 'csrf', 'perm:usuarios_gestionar']);

// Rutas de Configuración
$router->add('GET', '/configuracion', 'ConfiguracionController', 'index', ['auth', 'perm:configuracion_gestionar']);
$router->add('GET', '/configuracion/permisos', 'PermisoController', 'index', ['auth', 'perm:permisos_gestionar']);
$router->add('POST', '/configuracion/permisos/guardar', 'PermisoController', 'guardar', ['auth', 'csrf', 'perm:permisos_gestionar']);
$router->add('POST', '/configuracion/guardar', 'ConfiguracionController', 'guardar', ['auth', 'csrf', 'perm:configuracion_gestionar']);

// Rutas de Gestión de Roles
$router->add('GET', '/configuracion/roles', 'RolController', 'index', ['auth', 'perm:permisos_gestionar']);
$router->add('POST', '/configuracion/roles/crear', 'RolController', 'crear', ['auth', 'csrf', 'perm:permisos_gestionar']);
$router->add('POST', '/configuracion/roles/editar', 'RolController', 'editar', ['auth', 'csrf', 'perm:permisos_gestionar']);
$router->add('POST', '/configuracion/roles/eliminar', 'RolController', 'eliminar', ['auth', 'csrf', 'perm:permisos_gestionar']);

// Rutas de Mantenimiento Administrativo (Solo Administrador)
$router->add('GET', '/departamentos', 'DepartamentoController', 'index', ['auth', 'role:administrador']);
$router->add('POST', '/departamentos/guardar', 'DepartamentoController', 'guardar', ['auth', 'csrf', 'role:administrador']);
$router->add('POST', '/departamentos/actualizar', 'DepartamentoController', 'actualizar', ['auth', 'csrf', 'role:administrador']);
$router->add('POST', '/departamentos/estado/{id}', 'DepartamentoController', 'cambiarEstado', ['auth', 'csrf', 'role:administrador']);

$router->add('GET', '/tipos-solicitudes', 'TipoSolicitudController', 'index', ['auth', 'role:administrador']);
$router->add('POST', '/tipos-solicitudes/guardar', 'TipoSolicitudController', 'guardar', ['auth', 'csrf', 'role:administrador']);
$router->add('POST', '/tipos-solicitudes/actualizar', 'TipoSolicitudController', 'actualizar', ['auth', 'csrf', 'role:administrador']);
$router->add('POST', '/tipos-solicitudes/estado/{id}', 'TipoSolicitudController', 'cambiarEstado', ['auth', 'csrf', 'role:administrador']);

// Rutas AJAX (Para popups de mantenimiento rápido)
$router->add('POST', '/departamentos/guardar-ajax', 'DepartamentoController', 'guardarAjax', ['auth', 'csrf']);
$router->add('POST', '/tipos-solicitudes/guardar-ajax', 'TipoSolicitudController', 'guardarAjax', ['auth', 'csrf']);

// Rutas de Notificaciones
$router->add('GET', '/notificaciones/obtenerRecientes', 'NotificacionController', 'obtenerRecientes', ['auth']);
$router->add('GET', '/notificaciones/marcarLeida/{id}', 'NotificacionController', 'marcarLeida', ['auth']);
$router->add('GET', '/notificaciones/marcarTodas', 'NotificacionController', 'marcarTodas', ['auth']);


// Catch-all/Default route
$router->add('GET', '/', 'AuthController', 'mostrarLogin');

// Dispatch
$uri = $_SERVER['REQUEST_URI'];
$base_url = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$path = str_replace($base_url, '', parse_url($uri, PHP_URL_PATH));

// For local development where the path might be /SIGEDOC/public/...
$path = '/' . ltrim($path, '/');

$router->dispatch($path, $_SERVER['REQUEST_METHOD']);

