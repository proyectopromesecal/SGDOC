<?php
/**
 * Script de prueba de conectividad LDAP
 * Ejecutar con: php public/test_ldap_config.php <usuario> <password>
 */
require_once __DIR__ . '/../config.php';
spl_autoload_register(function ($class) {
    if (strpos($class, 'App\\') === 0) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) require $file;
    }
});

use App\Services\LdapService;

if ($argc < 3) {
    die("Uso: php public/test_ldap_config.php <usuario> <password>\n");
}

$usuario = $argv[1];
$password = $argv[2];

echo "Configuración LDAP detectada:\n";
echo "Host: " . LDAP_HOST . "\n";
echo "Port: " . LDAP_PORT . "\n";
echo "Domain: " . LDAP_DOMAIN . "\n";
echo "Base DN: " . LDAP_BASE_DN . "\n";
echo "-------------------------------\n";

$ldap = new LdapService();
echo "Intentando autenticar a '$usuario'...\n";

$resultado = $ldap->autenticar($usuario, $password);

if ($resultado) {
    echo "¡ÉXITO! Autenticación correcta.\n";
    echo "Datos obtenidos:\n";
    print_r($resultado);
} else {
    echo "FALLO: No se pudo autenticar. Verifique las credenciales o el estado del servidor LDAP.\n";
}
