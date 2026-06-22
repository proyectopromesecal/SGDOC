<?php
namespace App\Middlewares;

use App\Core\Middleware;

class RoleMiddleware implements Middleware {
    public function handle($params = []) {
        if (empty($params)) {
            return;
        }

        $rolesRequeridos = array_map('strtolower', $params);
        $rolUsuario = isset($_SESSION['rol_nombre']) ? strtolower($_SESSION['rol_nombre']) : '';

        // SuperAdmin bypass
        if (strpos($rolUsuario, 'admin') !== false) {
            return;
        }

        if (!in_array($rolUsuario, $rolesRequeridos)) {
            http_response_code(403);
            if (file_exists(__DIR__ . '/../../views/errors/403.php')) {
                require_once __DIR__ . '/../../views/errors/403.php';
            } else {
                $_SESSION['error'] = 'Acceso denegado: Rol no autorizado.';
                header('Location: /dashboard');
            }
            exit;
        }
    }
}
