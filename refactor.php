<?php
$file = 'c:\Temp\SIGEDOC\app\Controllers\DocumentoController.php';
$content = file_get_contents($file);

// 1. Replace role string checks with Permission checks
$content = preg_replace("/\\$_SESSION\['rol_nombre'\] === 'Encargado de departamento'/", "\\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_encargado')", $content);
$content = preg_replace("/\\$_SESSION\['rol_nombre'\] !== 'Encargado de departamento'/", "!\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_encargado')", $content);

$content = preg_replace("/\\$_SESSION\['rol_nombre'\] === 'Jefe de departamento'/", "\\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_jefe')", $content);
$content = preg_replace("/\\$_SESSION\['rol_nombre'\] !== 'Jefe de departamento'/", "!\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_jefe')", $content);

$content = preg_replace("/\\$_SESSION\['rol_nombre'\] === 'Gerencia'/", "\\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_gerencia')", $content);
$content = preg_replace("/\\$_SESSION\['rol_nombre'\] !== 'Gerencia'/", "!\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_gerencia')", $content);

// In case of combined checks (Jefe, Gerencia, Encargado)
$content = preg_replace("/!\\\\App\\\\Controllers\\\\AuthController::tienePermiso\('documentos_autorizar_jefe'\) && !\\\\App\\\\Controllers\\\\AuthController::tienePermiso\('documentos_autorizar_gerencia'\) && !\\\\App\\\\Controllers\\\\AuthController::tienePermiso\('documentos_autorizar_encargado'\)/", "!( \App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') || \App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia') || \App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado') )", $content);

// 2. Replace rol_destinatario with permiso_destinatario strings
$content = str_replace("'rol_destinatario' => 'Encargado de departamento'", "'rol_destinatario' => 'documentos_autorizar_encargado'", $content);
$content = str_replace("'rol_destinatario' => 'Jefe de departamento'", "'rol_destinatario' => 'documentos_autorizar_jefe'", $content);
$content = str_replace("'rol_destinatario' => 'Gerencia'", "'rol_destinatario' => 'documentos_autorizar_gerencia'", $content);

// 3. Fix the array_key check for creador_habilitado
$content = preg_replace("/in_array\(\\$rolActual, \['Secretaria', 'Jefe de departamento'\]\)/", "(\\App\\Controllers\\AuthController::tienePermiso('documentos_gestionar_propios') || \\App\\Controllers\\AuthController::tienePermiso('documentos_autorizar_jefe'))", $content);

// 4. Update Notifications variables
$content = str_replace("Autorizado por \" . \$_SESSION['rol_nombre'] . \".", "Autorizado.\"", $content);
$content = str_replace("Documento autorizado por \" . \$_SESSION['rol_nombre'] . \":", "Documento autorizado:", $content);

file_put_contents($file, $content);
echo "DocumentoController modificado.";
