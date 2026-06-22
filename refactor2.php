<?php
$file = 'c:\Temp\SIGEDOC\app\Controllers\DocumentoController.php';
$content = file_get_contents($file);

$reps = [
    "if (\$_SESSION['rol_nombre'] === 'Encargado de departamento')" => "if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado'))",
    "if (\$_SESSION['rol_nombre'] !== 'Encargado de departamento' && !\$isAdmin)" => "if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado') && !\$isAdmin)",
    
    "if (\$_SESSION['rol_nombre'] === 'Jefe de departamento')" => "if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe'))",
    "if (\$_SESSION['rol_nombre'] !== 'Jefe de departamento' && !\$isAdmin)" => "if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') && !\$isAdmin)",
    
    "if (\$_SESSION['rol_nombre'] === 'Gerencia')" => "if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia'))",
    "if (\$_SESSION['rol_nombre'] !== 'Gerencia' && !\$isAdmin)" => "if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia') && !\$isAdmin)",
    
    "if (\$_SESSION['rol_nombre'] !== 'Jefe de departamento' && \$_SESSION['rol_nombre'] !== 'Gerencia' && \$_SESSION['rol_nombre'] !== 'Encargado de departamento' && !\$isAdmin)" => 
    "if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') && !\App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia') && !\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado') && !\$isAdmin)",
    
    "'rol_destinatario' => 'Encargado de departamento'" => "'rol_destinatario' => 'documentos_autorizar_encargado'",
    "'rol_destinatario' => 'Jefe de departamento'" => "'rol_destinatario' => 'documentos_autorizar_jefe'",
    "'rol_destinatario' => 'Gerencia'" => "'rol_destinatario' => 'documentos_autorizar_gerencia'",
    
    "in_array(\$rolActual, ['Secretaria', 'Jefe de departamento'])" => "(\App\Controllers\AuthController::tienePermiso('documentos_gestionar_propios') || \App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe'))",
];

foreach($reps as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Modificado correctamente.";
