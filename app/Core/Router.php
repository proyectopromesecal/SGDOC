<?php
namespace App\Core;

class Router {
    protected $routes = [];

    protected $middlewareAliases = [
        'auth' => \App\Middlewares\AuthMiddleware::class,
        'perm' => \App\Middlewares\PermissionMiddleware::class,
        'role' => \App\Middlewares\RoleMiddleware::class,
        'csrf' => \App\Middlewares\CsrfMiddleware::class
    ];

    public function add($method, $route, $controller, $action, $middlewares = []) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch($uri, $method) {
        // Clean URI from base path if needed
        $uri = explode('?', $uri)[0];
        
        foreach ($this->routes as $route) {
            // Convertir ruta con parámetros a regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route['route']);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $uri, $matches) && $route['method'] === $method) {
                // Remover el primer elemento (la coincidencia completa)
                array_shift($matches);
                
                // Ejecutar Middlewares
                foreach ($route['middlewares'] as $middlewareString) {
                    $parts = explode(':', $middlewareString);
                    $alias = $parts[0];
                    $params = isset($parts[1]) ? explode(',', $parts[1]) : [];
                    
                    if (isset($this->middlewareAliases[$alias])) {
                        $middlewareClass = $this->middlewareAliases[$alias];
                        $middlewareInstance = new $middlewareClass();
                        $middlewareInstance->handle($params);
                    }
                }
                
                $controllerName = "App\\Controllers\\" . $route['controller'];
                $action = $route['action'];
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $action)) {
                        // Pasar parámetros al método del controlador
                        return call_user_func_array([$controller, $action], $matches);
                    }
                }
            }
        }

        // Default to 404
        http_response_code(404);
        if (file_exists(__DIR__ . '/../../views/errors/404.php')) {
            require_once __DIR__ . '/../../views/errors/404.php';
        } else {
            echo "404 Not Found - Route: $uri ($method)";
        }
        exit;
    }
}

