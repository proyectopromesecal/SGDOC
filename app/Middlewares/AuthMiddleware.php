<?php
namespace App\Middlewares;

use App\Core\Middleware;

class AuthMiddleware implements Middleware {
    public function handle($params = []) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Excepción de bucle infinito para logout
        if ($currentPath === '/logout') {
            return;
        }

        // Si el usuario está pendiente de acceso (rol 6), debe ir obligatoriamente a solicitud-acceso
        if ($_SESSION['rol_id'] == 6 && $currentPath !== '/solicitud-acceso') {
            header('Location: /solicitud-acceso');
            exit;
        }

        // Si tiene rol asignado y trata de ver solicitud-acceso, redirigir al dashboard
        if ($_SESSION['rol_id'] != 6 && $currentPath === '/solicitud-acceso') {
            header('Location: /dashboard');
            exit;
        }
    }
}
