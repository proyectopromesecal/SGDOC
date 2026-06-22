<?php
namespace App\Core;

interface Middleware {
    /**
     * Define la lógica de la capa de seguridad.
     * Si la validación falla, debe redirigir o lanzar una excepción y terminar la ejecución (exit).
     * 
     * @param array $params Parámetros adicionales configurados en la ruta
     */
    public function handle($params = []);
}
