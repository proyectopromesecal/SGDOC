<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    $stmt2 = $db->query("SELECT * FROM tipos_solicitudes");
    $data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "\nTotal rows in tipos_solicitudes: " . count($data) . "\n";
    print_r($data);

    if ($hasTipos) {
        $stmt2 = $db->query("SELECT * FROM $hasTipos");
        echo "\nData in $hasTipos:\n";
        print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
