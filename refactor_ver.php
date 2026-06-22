<?php
$file = 'c:\Temp\SIGEDOC\views\documentos\ver.php';
$content = file_get_contents($file);

$reps = [
    "\$esGerencia = (\$rol === 'Gerencia');" => "\$esGerencia = \App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia');",
    "\$esEncargado  = (\$rol === 'Encargado de departamento');" => "\$esEncargado = \App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado');",
    "\$esSecretaria = (\$rol === 'Secretaria');" => "\$esSecretaria = \App\Controllers\AuthController::tienePermiso('documentos_gestionar_propios');",
    "if (in_array(\$_SESSION['rol_nombre'], ['Jefe de departamento', 'Administrador']))" => "if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') || \$isAdmin)",
    "\$rol = \$_SESSION['rol_nombre'] ?? '';" => "\$rol = \$_SESSION['rol_nombre'] ?? '';\n                    \$isAdmin = (strpos(strtolower(\$_SESSION['rol_nombre'] ?? ''), 'admin') !== false);"
];

foreach($reps as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);
echo "Vista ver.php modificada correctamente.";
