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
        
        $str = str_replace([' y ', ' e ', ' para ', ' la ', ' de ', ' del '], ' ', $str);
        $str = preg_replace('/[^a-z0-9]/', '', $str);
        return trim($str);
    }

    $groups = [];
    foreach ($deps as $d) {
        $norm = normalize($d['nombre']);
        $groups[$norm][] = $d;
    }

    $db->beginTransaction();

    $total_grupos_limpiados = 0;
    $total_duplicados_borrados = 0;

    foreach ($groups as $norm => $items) {
        if (count($items) > 1) {
            // Find canonical ID (we'll use the lowest ID as the original)
            $canonical_id = $items[0]['id'];
            $canonical_name = $items[0]['nombre'];
            
            // Optionally, we can try to find the longest name to keep the most descriptive one
            // but we assign it to the canonical_id
            $best_name = $canonical_name;
            foreach ($items as $item) {
                if (strlen($item['nombre']) > strlen($best_name)) {
                    $best_name = $item['nombre'];
                }
            }

            // Let's update the canonical record to have the best name
            $stmtUpdateName = $db->prepare("UPDATE departamentos SET nombre = ? WHERE id = ?");
            $stmtUpdateName->execute([$best_name, $canonical_id]);

            $duplicate_ids = [];
            foreach ($items as $idx => $item) {
                if ($idx > 0) { // Skip the first one which is canonical
                    $duplicate_ids[] = (int)$item['id'];
                }
            }

            if (!empty($duplicate_ids)) {
                $placeholders = implode(',', array_fill(0, count($duplicate_ids), '?'));
                
                // Update documentos.departamento_destino_id
                $params = array_merge([$canonical_id], $duplicate_ids);
                $stmt1 = $db->prepare("UPDATE documentos SET departamento_destino_id = ? WHERE departamento_destino_id IN ($placeholders)");
                $stmt1->execute($params);

                // Update documentos.departamento_origen_id
                $stmt2 = $db->prepare("UPDATE documentos SET departamento_origen_id = ? WHERE departamento_origen_id IN ($placeholders)");
                $stmt2->execute($params);

                // Update usuarios.departamento_id
                $stmt3 = $db->prepare("UPDATE usuarios SET departamento_id = ? WHERE departamento_id IN ($placeholders)");
                $stmt3->execute($params);

                // Now safe to delete
                $stmtDelete = $db->prepare("DELETE FROM departamentos WHERE id IN ($placeholders)");
                $stmtDelete->execute($duplicate_ids);

                $total_duplicados_borrados += count($duplicate_ids);
                $total_grupos_limpiados++;

                echo "Grupo resuelto: $best_name (Mantuvo ID $canonical_id, Borró IDs: " . implode(',', $duplicate_ids) . ")\n";
            }
        }
    }

    $db->commit();
    echo "\n¡Limpieza completada con éxito!\n";
    echo "Grupos limpiados: $total_grupos_limpiados\n";
    echo "Departamentos duplicados borrados: $total_duplicados_borrados\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
}
