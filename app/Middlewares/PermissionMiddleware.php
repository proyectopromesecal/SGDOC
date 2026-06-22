<?php
namespace App\Middlewares;

use App\Core\Middleware;

class PermissionMiddleware implements Middleware {
    public function handle($params = []) {
        if (empty($params)) {
            return;
        }

        // El primer parámetro es el nombre del permiso (ej: auth:perm:documentos_listar)
        $permiso = $params[0];

        // SuperAdmin bypass
        if (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false) {
            return;
        }

        if (!isset($_SESSION['permisos'])) {
            if (isset($_SESSION['usuario_id']) && isset($_SESSION['rol_id'])) {
                $usuarioModel = new \App\Models\Usuario();
                $_SESSION['permisos'] = $usuarioModel->obtenerPermisos($_SESSION['rol_id']);
            } else {
                header('Location: /login');
                exit;
            }
        }

        if (!in_array($permiso, $_SESSION['permisos'])) {
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
}
