<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT id, nombre FROM departamentos ORDER BY id ASC");
    $deps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    function normalize($str) {
        $str = mb_strtolower(trim($str), 'UTF-8');
        // Remove accents
        $unwanted_array = array(
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
            'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
            'ä'=>'a', 'ë'=>'e', 'ï'=>'i', 'ö'=>'o', 'ü'=>'u',
            'ñ'=>'n'
        );
        $str = strtr($str, $unwanted_array);

        // Remove prefixes
        $prefixes = [
            'departamento de', 'depto. de', 'depto.', 'depto',
            'direccion general de', 'direccion de', 'direccion', 
            'seccion de', 'seccion',
            'division de', 'division',
            'oficina de', 'oficina',
            'unidad de', 'unidad'
        ];
        
        foreach ($prefixes as $prefix) {
            if (strpos($str, $prefix) === 0) {
                $str = substr($str, strlen($prefix));
                break;
            }
        }
        
        // Remove common filler words and non-alphanumeric
        $str = str_replace([' y ', ' e ', ' para ', ' la ', ' de ', ' del '], ' ', $str);
        $str = preg_replace('/[^a-z0-9]/', '', $str);
        return trim($str);
    }

    $groups = [];
    foreach ($deps as $d) {
        $norm = normalize($d['nombre']);
        $groups[$norm][] = $d;
    }

    echo "Grupos encontrados (Duplicados):\n";
    foreach ($groups as $norm => $items) {
        if (count($items) > 1) {
            echo "\nGrupo: $norm\n";
            foreach ($items as $item) {
                echo " - [{$item['id']}] {$item['nombre']}\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
