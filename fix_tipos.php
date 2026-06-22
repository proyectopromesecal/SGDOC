<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    
    // Check if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM tipos_solicitudes");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $db->query("INSERT INTO tipos_solicitudes (nombre, descripcion, activo) VALUES ('Otros', 'Tipos de solicitudes varias', 1)");
        $db->query("INSERT INTO tipos_solicitudes (nombre, descripcion, activo) VALUES ('Adquisición de Bienes', 'Compra de bienes', 1)");
        $db->query("INSERT INTO tipos_solicitudes (nombre, descripcion, activo) VALUES ('Contratación de Servicios', 'Contratos', 1)");
        echo "Tipos de solicitudes insertados correctamente.\n";
    } else {
        echo "La tabla ya tiene datos ($count).\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
