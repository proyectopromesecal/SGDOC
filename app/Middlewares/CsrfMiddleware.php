<?php
namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Security;

class CsrfMiddleware implements Middleware {
    public function handle($params = []) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!Security::validateCsrfToken($token)) {
                http_response_code(403);
                echo "Error de Seguridad: Token CSRF inválido o expirado.";
                exit;
            }
        }
    }
}
