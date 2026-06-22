<?php
/**
 * SIGEDOC - Script de Sincronización Directa de Usuarios LDAP
 * Subir este archivo a la carpeta /public/ del servidor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Configuración de Conexión (Ajustada a tu servidor)
$db_host = "172.125.70.73";
$db_user = "usigedoc";
$db_pass = "5VT8DF4qeCqmaBqos5%&Ge#a@";
$db_name = "sigedoc";

$ldap_host = "promese.promesecal.gob.do";
$ldap_dn   = "DC=promese,DC=promesecal,DC=gob,DC=do";

echo "<h2>--- Sincronizador de Usuarios SIGEDOC ---</h2>";

try {
    // 2. Conectar a SQL Server usando PDO
    $dsn = "sqlsrv:Server=$db_host;Database=$db_name;Encrypt=no;TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✅ Conexión a Base de Datos: EXITOSA</p>";

    // 3. Conectar a LDAP
    $ds = ldap_connect($ldap_host);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);

    // Intentar bind anónimo para búsqueda
    // Si el servidor AD de Promese no permite bind anónimo, este script se detendrá aquí.
    if (!@ldap_bind($ds)) {
        die("<p style='color:red'>❌ ERROR: El servidor LDAP no permite búsquedas anónimas. El servidor requiere credenciales de sistema para listar usuarios.</p>");
    }
    echo "<p style='color:green'>✅ Conexión a LDAP: EXITOSA</p>";

    // 4. Buscar Usuarios
    $filter = "(objectClass=user)";
    $attributes = ["samaccountname", "displayname", "mail", "department", "title"];
    $search = ldap_search($ds, $ldap_dn, $filter, $attributes);
    
    if (!$search) {
        die("<p style='color:red'>❌ ERROR: No se pudo realizar la búsqueda en LDAP.</p>");
    }
    
    $entries = ldap_get_entries($ds, $search);

    echo "<p><b>Usuarios encontrados en el Directorio Activo:</b> " . ($entries["count"] ?? 0) . "</p>";
    echo "<div style='background:#f4f4f4; padding:10px; border:1px solid #ccc; max-height:400px; overflow-y:auto;'><ul>";

    $nuevos = 0;
    $existentes = 0;

    for ($i=0; $i < $entries["count"]; $i++) {
        $usuario = $entries[$i]["samaccountname"][0] ?? '';
        if (empty($usuario)) continue;

        // Verificar si ya existe en la BD
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        
        if (!$stmt->fetch()) {
            $nombre = $entries[$i]["displayname"][0] ?? $usuario;
            $email = $entries[$i]["mail"][0] ?? '';
            $dept = $entries[$i]["department"][0] ?? '';
            $cargo = $entries[$i]["title"][0] ?? '';
            
            // Rol 6 por defecto (Pendiente de Acceso)
            $sql = "INSERT INTO usuarios (usuario, password, rol_id, nombre, email, departamento, cargo, tipo_auth) 
                    VALUES (?, 'LDAP_LOCKED', 6, ?, ?, ?, ?, 'LDAP')";
            $pdo->prepare($sql)->execute([$usuario, $nombre, $email, $dept, $cargo]);
            
            echo "<li style='color:blue'>[NUEVO] $usuario - $nombre</li>";
            $nuevos++;
        } else {
            $existentes++;
        }
    }

    echo "</ul></div>";
    echo "<h3>Resumen:</h3>";
    echo "<ul><li>Usuarios nuevos creados: $nuevos</li><li>Usuarios ya existentes: $existentes</li></ul>";
    echo "<p style='color:green; font-weight:bold;'>✔ Sincronización finalizada correctamente.</p>";

    ldap_unbind($ds);

} catch (Exception $e) {
    echo "<p style='color:red'>❌ ERROR FATAL: " . $e->getMessage() . "</p>";
}
?>
