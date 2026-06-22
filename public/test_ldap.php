<?php
// DIAGNÓSTICO LDAP - Borrar después de usar
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO LDAP ===\n\n";

// 1. Extensión LDAP
echo "1. Extensión LDAP cargada: " . (extension_loaded('ldap') ? "SÍ ✓" : "NO ✗") . "\n";

// 2. Configuración
$host   = defined('LDAP_HOST')      ? LDAP_HOST      : 'promese.promesecal.gob.do';
$port   = defined('LDAP_PORT')      ? LDAP_PORT      : 389;
$baseDn = defined('LDAP_BASE_DN')   ? LDAP_BASE_DN   : '';
$domain = defined('LDAP_DOMAIN')    ? LDAP_DOMAIN    : '';
$bUser  = defined('LDAP_BIND_USER') ? LDAP_BIND_USER : '';
$bPass  = defined('LDAP_BIND_PASS') ? LDAP_BIND_PASS : '';

echo "2. Host    : $host\n";
echo "3. Puerto  : $port\n";
echo "4. Base DN : $baseDn\n";
echo "5. Dominio : $domain\n";
echo "6. Bind User configurado: " . (!empty($bUser) ? "SÍ ($bUser)" : "NO - VACÍO ✗") . "\n";
echo "7. Bind Pass configurado: " . (!empty($bPass) ? "SÍ (***)" : "NO - VACÍO ✗") . "\n\n";

// 3. Conectar
echo "=== PRUEBA DE CONEXIÓN ===\n";
$ds = @ldap_connect($host, (int)$port);
if (!$ds) {
    echo "FALLO: No se pudo crear el recurso de conexión.\n";
    exit;
}
echo "ldap_connect()  : OK ✓\n";

ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 5);

// 4. Bind
echo "\n=== PRUEBA DE AUTENTICACIÓN (BIND) ===\n";
if (!empty($bUser) && !empty($bPass)) {
    $ldapUser = (strpos($bUser, '@') === false) ? $bUser . '@' . $domain : $bUser;
    echo "Intentando bind como: $ldapUser\n";
    $bind = @ldap_bind($ds, $ldapUser, $bPass);
    if ($bind) {
        echo "ldap_bind()     : OK ✓\n\n";

        // 5. Búsqueda de prueba
        echo "=== PRUEBA DE BÚSQUEDA ===\n";
        $filter = "(samaccountname=adoperator)";
        $attrs  = ['displayname','mail','department','title'];
        $search = @ldap_search($ds, $baseDn, $filter, $attrs);
        if ($search) {
            $entries = ldap_get_entries($ds, $search);
            echo "Registros encontrados : " . $entries['count'] . "\n";
            if ($entries['count'] > 0) {
                echo "Nombre  : " . ($entries[0]['displayname'][0] ?? 'N/A') . "\n";
                echo "Email   : " . ($entries[0]['mail'][0] ?? 'N/A') . "\n";
                echo "Depto.  : " . ($entries[0]['department'][0] ?? 'N/A') . "\n";
                echo "\nBÚSQUEDA LDAP FUNCIONA CORRECTAMENTE ✓\n";
            }
        } else {
            $err = ldap_error($ds);
            echo "FALLO búsqueda: $err ✗\n";
        }
    } else {
        $errno = ldap_errno($ds);
        $err   = ldap_error($ds);
        echo "FALLO bind: [$errno] $err ✗\n";
        echo "Posibles causas:\n";
        echo " - Contraseña incorrecta\n";
        echo " - Usuario no existe en el dominio\n";
        echo " - El DC no acepta el formato usuario@dominio (prueba con DOMINIO\\usuario)\n";
    }
} else {
    echo "Sin credenciales configuradas — probando bind anónimo...\n";
    $bind = @ldap_bind($ds);
    echo $bind ? "Bind anónimo: OK ✓\n" : "Bind anónimo: FALLO ✗ (el servidor requiere credenciales)\n";
}

@ldap_unbind($ds);
echo "\n=== FIN DIAGNÓSTICO ===\n";
?>
