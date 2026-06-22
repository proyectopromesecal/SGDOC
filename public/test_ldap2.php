<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: text/plain; charset=utf-8');

$host   = LDAP_HOST;
$port   = (int)LDAP_PORT;
$baseDn = LDAP_BASE_DN;
$pass   = LDAP_BIND_PASS;

$ds = @ldap_connect($host, $port);
ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 5);

$formatos = [
    'adoperator@promese.promesecal.gob.do',
    'adoperator@promese',
    'PROMESE\adoperator',
    'promese\adoperator',
];

echo "Base DN leído: $baseDn\n\n";

foreach ($formatos as $fmt) {
    echo "Probando: $fmt\n";
    $b = @ldap_bind($ds, $fmt, $pass);
    echo "Resultado: " . ($b ? "OK ✓" : "[" . ldap_errno($ds) . "] " . ldap_error($ds) . " ✗") . "\n\n";
    if ($b) {
        // Buscar algo de prueba
        $search = @ldap_search($ds, $baseDn, "(samaccountname=*)", ['displayname'], 0, 3);
        if ($search) {
            $entries = ldap_get_entries($ds, $search);
            echo "  Búsqueda de prueba: " . $entries['count'] . " registros encontrados.\n";
            for ($i = 0; $i < min(3, $entries['count']); $i++) {
                echo "  - " . ($entries[$i]['displayname'][0] ?? '???') . "\n";
            }
        }
        break;
    }
}

@ldap_unbind($ds);
echo "\nFIN\n";
