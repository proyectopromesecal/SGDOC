<?php
namespace App\Controllers;

use App\Models\Documento;
use App\Models\Bitacora;
use App\Models\Configuracion;
use App\Models\Adjunto;
use App\Models\Usuario;
use App\Models\Notificacion;
use App\Models\Seguimiento;
use App\Core\FirmaDigital;

class DocumentoController {
    private $documentoModel;
    private $bitacoraModel;
    private $firmaDigital;
    private $configModel;
    private $adjuntoModel;
    private $usuarioModel;
    private $notificacionModel;
    private $seguimientoModel;
    
    public function __construct() {
        $this->documentoModel = new Documento();
        $this->bitacoraModel = new Bitacora();
        $this->firmaDigital = new FirmaDigital();
        $this->configModel = new Configuracion();
        $this->adjuntoModel = new Adjunto();
        $this->usuarioModel = new Usuario();
        $this->notificacionModel = new Notificacion();
        $this->seguimientoModel = new Seguimiento();
    }
    
    /**
     * Listar documentos
     */
    public function listar() {
        $filtros = [];
        $rol = $_SESSION['rol_nombre'] ?? '';
        
        // Filtrar por permisos
        if (!AuthController::tienePermiso('documentos_ver')) {
            $filtros['usuario_id'] = $_SESSION['usuario_id'];
        }
        
        // Secretaria: Solo ve los documentos que ella misma creó
        if ($rol === 'Secretaria') {
            $filtros['usuario_id'] = $_SESSION['usuario_id'];
        }
        
        // Encargado de departamento: Ve docs SOLICITADOS de su propio departamento (para autorizar)
        if ($rol === 'Encargado de departamento') {
            $filtros['departamento_origen_id'] = $_SESSION['departamento_id'] ?? null;
            $filtros['departamento_encargado_origen'] = $_SESSION['departamento'] ?? 'N/A';
            $filtros['estado_encargado'] = 'SOLICITADO';
        }
        
        // Jefe de departamento: Ve docs AUTORIZADO_ENCARGADO dirigidos a su departamento destino
        if ($rol === 'Jefe de departamento') {
            $filtros['departamento_destino_id_jefe'] = $_SESSION['departamento_id'] ?? null;
            $filtros['departamento_encargado'] = $_SESSION['departamento'] ?? 'N/A';
            $filtros['estado_jefe'] = 'AUTORIZADO_ENCARGADO';
        }
        
        if (isset($_GET['estado'])) {
            $filtros['estado'] = $_GET['estado'];
        }

        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 10;

        $documentos = $this->documentoModel->obtenerTodos($filtros, $pagina, $limite);
        $totalRegistros = $this->documentoModel->contarTodos($filtros);
        $totalPaginas = ceil($totalRegistros / $limite);

        $estadisticas = $this->documentoModel->obtenerEstadisticas(
            in_array($rol, ['Secretaria', 'Jefe de departamento', 'Encargado de departamento']) ? $_SESSION['usuario_id'] : null
        );
        
        require_once __DIR__ . '/../../views/documentos/listar.php';
    }
    
    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        $deptoModel = new \App\Models\Departamento();
        $tipoSolicitudModel = new \App\Models\TipoSolicitud();
        
        $departamentos = $deptoModel->obtenerActivos();
        $tiposSolicitud = $tipoSolicitudModel->obtenerActivos();
        
        require_once __DIR__ . '/../../views/documentos/crear.php';
    }

    /**
     * Mostrar formulario para digitalizados
     */
    public function digitalizados() {
        require_once __DIR__ . '/../../views/documentos/digitalizados.php';
    }
    
    /**
     * Guardar nuevo documento
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /documentos');
            exit;
        }
        
        // Validar datos
        $id = $_POST['id'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $tipo_solicitud_id = $_POST['tipo_solicitud_id'] ?? '';
        $departamento_destino_id = $_POST['departamento_destino_id'] ?? '';
        
        if (empty($id) || empty($descripcion) || empty($tipo_solicitud_id) || empty($departamento_destino_id)) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            header('Location: /documentos/crear');
            exit;
        }

        // Obtener el nombre del tipo de solicitud para mantener compatibilidad hacia atrás
        $tipoSolicitudModel = new \App\Models\TipoSolicitud();
        $tipoObj = $tipoSolicitudModel->obtenerPorId($tipo_solicitud_id);
        $tipo = $tipoObj ? $tipoObj['nombre'] : 'Otros';
        
        // Verificar si el ID ya existe
        if ($this->documentoModel->existeId($id)) {
            $_SESSION['error'] = 'El ID del documento ya existe';
            header('Location: /documentos/crear');
            exit;
        }
        
        // Manejar archivo
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Debe seleccionar un archivo';
            header('Location: /documentos/crear');
            exit;
        }
        
        $archivo = $_FILES['archivo'];
        $config = $this->configModel->obtenerTodas();
        
        // Validar Tamaño Forzado a 100MB (Ignorar configuración BD para soporte de grandes montos temporalmente)
        $maxSizeMB = 100;
        if ($archivo['size'] > ($maxSizeMB * 1024 * 1024)) {
            $_SESSION['error'] = "El archivo excede el tamaño máximo permitido ($maxSizeMB MB)";
            header('Location: /documentos/crear');
            exit;
        }

        // Validar Extensiones
        // Validar Extensiones (Solo PDF)
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            $_SESSION['error'] = 'Solo se permiten archivos PDF.';
            header('Location: /documentos/crear');
            exit;
        }

        // Validar MIME Type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            $_SESSION['error'] = 'El archivo no es un PDF válido.';
            header('Location: /documentos/crear');
            exit;
        }

        $nombreArchivo = $id . '_' . time() . '.' . $extension;
        
        // Ruta de destino desde configuración o default
        $basePath = $config['path_originales'] ?? '';
        if (empty($basePath) || !is_dir($basePath) || !is_writable($basePath)) {
            $basePath = STORAGE_PATH . '/documentos/';
        }
        
        $rutaDestino = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $nombreArchivo;
        
        // Crear directorio si no existe (solo si es el default o tenemos permisos)
        $directorioDestino = dirname($rutaDestino);
        if (!is_dir($directorioDestino)) {
            @mkdir($directorioDestino, 0755, true);
        }
        
        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $_SESSION['error'] = 'Error al subir el archivo al servidor. Verifique permisos de escritura.';
            header('Location: /documentos/crear');
            exit;
        }
        
        // Guardar en base de datos — incluir departamento_origen_id del usuario actual
        $datos = [
            'id' => $id,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'ruta_original' => $nombreArchivo,
            'usuario_id' => $_SESSION['usuario_id'],
            'prioridad' => $_POST['prioridad'] ?? 'Normal',
            'tipo_solicitud_id' => $tipo_solicitud_id,
            'departamento_destino_id' => $departamento_destino_id,
            'departamento_origen_id' => $_SESSION['departamento_id'] ?? null
        ];
        
        if ($this->documentoModel->crear($datos)) {
            // Manejar adjuntos de soporte adicionales
            if (isset($_FILES['adjuntos']) && !empty($_FILES['adjuntos']['name'][0])) {
                $soportePath = $config['path_soporte'] ?? '';
                if (empty($soportePath) || !is_dir($soportePath) || !is_writable($soportePath)) {
                    $soportePath = STORAGE_PATH . '/documentos/';
                }
                $soportePath = rtrim($soportePath, '/\\') . DIRECTORY_SEPARATOR;

                foreach ($_FILES['adjuntos']['name'] as $key => $name) {
                    if ($_FILES['adjuntos']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmpName = $_FILES['adjuntos']['tmp_name'][$key];
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $nombreSeguro = $id . '_SOPORTE_' . $key . '_' . time() . '.' . $ext;
                        
                        if (move_uploaded_file($tmpName, $soportePath . $nombreSeguro)) {
                            $this->adjuntoModel->registrar($id, $nombreSeguro);
                        }
                    }
                }
            }

            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CREAR_DOCUMENTO',
                "Documento creado: $id"
            );
            $_SESSION['success'] = 'Documento registrado exitosamente';

            // Notificar al Encargado de departamento ORIGEN (quien debe autorizar primero)
            $deptoModel = new \App\Models\Departamento();
            $departamentoOrigenNombre = $_SESSION['departamento'] ?? '';

            $this->notificacionModel->crear([
                'rol_destinatario' => 'documentos_autorizar_encargado',
                'departamento' => $departamentoOrigenNombre,
                'titulo' => 'Nueva Solicitud Pendiente de Autorización',
                'mensaje' => "Nueva solicitud #$id de su departamento requiere su autorización.",
                'link' => "/documentos/ver/$id"
            ]);
            $this->notificacionModel->crear([
                'rol_destinatario' => 'Administrador',
                'titulo' => 'Nueva Solicitud',
                'mensaje' => "Nueva solicitud #$id en el sistema.",
                'link' => "/documentos/ver/$id"
            ]);

            // Registrar Seguimiento
            $this->seguimientoModel->registrar(
                $id, 
                $_SESSION['usuario_id'], 
                null, 
                'SOLICITADO', 
                'CREACION', 
                "Documento creado y enviado al Encargado de departamento para autorización."
            );

        } else {
            $_SESSION['error'] = 'Error al registrar el documento en la base de datos';
        }
        
        header('Location: /documentos');
        exit;
    }
    
    /**
     * Ver detalle del documento
     */
    public function ver($id) {
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }
        
        // Verificar permisos
        $adjuntos = $this->adjuntoModel->obtenerPorDocumento($id);
        $seguimiento = $this->seguimientoModel->obtenerPorDocumento($id);
        
        require_once VIEWS_PATH . '/documentos/ver.php';
    }
    
    /**
     * Helper para clasificar el flujo (A o B) según el nombre del tipo de solicitud
     */
    public static function obtenerFlujo($tipoNombre) {
        $tipo = strtolower($tipoNombre);
        if (strpos($tipo, 'compra') !== false || 
            strpos($tipo, 'adquisic') !== false || 
            strpos($tipo, 'personal') !== false || 
            strpos($tipo, 'vacacion') !== false || 
            strpos($tipo, 'permiso') !== false || 
            strpos($tipo, 'contrato') !== false || 
            strpos($tipo, 'presupuest') !== false) {
            return 'A';
        }
        return 'B';
    }

    /**
     * Autorizar documento — Paso 1: Encargado de departamento origen (SOLICITADO → AUTORIZADO_ENCARGADO)
     */
    public function autorizar_encargado($id) {
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        $isAdmin = (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false);
        
        if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado') && !$isAdmin) {
            $_SESSION['error'] = 'Solo el Encargado de departamento puede autorizar en este paso';
            header('Location: /documentos/ver/' . $id);
            exit;
        }

        // Auto-aprobación prohibida: el encargado no puede autorizar su propia solicitud
        if ($documento['usuario_id'] == $_SESSION['usuario_id'] && !$isAdmin) {
            $_SESSION['error'] = 'Auto-aprobación denegada: No puede autorizar su propia solicitud.';
            header('Location: /documentos/ver/' . $id);
            exit;
        }

        // Validar que el documento pertenece al departamento de origen del encargado
        if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado')) {
            $autorizadoOrigen = false;
            if (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_origen_id'])) {
                $autorizadoOrigen = ($_SESSION['departamento_id'] == $documento['departamento_origen_id']);
            } else {
                // Fallback por texto
                $autorizadoOrigen = ($_SESSION['departamento'] === ($documento['depto_solicitante'] ?? ''));
            }
            if (!$autorizadoOrigen) {
                $_SESSION['error'] = 'Este documento no pertenece a su departamento de origen';
                header('Location: /documentos');
                exit;
            }
        }

        if ($documento['estado'] !== 'SOLICITADO') {
            $_SESSION['error'] = 'El documento no está en estado SOLICITADO';
            header('Location: /documentos/ver/' . $id);
            exit;
        }

        // Actualizar estado: SOLICITADO → AUTORIZADO_ENCARGADO
        $this->documentoModel->actualizarEstado($id, 'AUTORIZADO_ENCARGADO');

        $this->bitacoraModel->registrar(
            $_SESSION['usuario_id'],
            'AUTORIZAR_ENCARGADO',
            "Solicitud autorizada por " . $_SESSION['rol_nombre'] . ": $id"
        );

        $_SESSION['success'] = 'Solicitud autorizada. Enviada al Jefe de departamento destino.';

        // Notificar al Jefe del departamento DESTINO
        $deptoModel = new \App\Models\Departamento();
        $deptoDestino = $deptoModel->obtenerPorId($documento['departamento_destino_id'] ?? 0);
        $deptoDestinoNombre = $deptoDestino ? $deptoDestino['nombre'] : '';

        $this->notificacionModel->crear([
            'rol_destinatario' => 'documentos_autorizar_jefe',
            'departamento' => $deptoDestinoNombre,
            'titulo' => 'Nueva Solicitud Autorizada Pendiente',
            'mensaje' => "La solicitud #$id ha sido autorizada por el Encargado de departamento y requiere su revisión.",
            'link' => "/documentos/ver/$id"
        ]);
        $this->notificacionModel->crear([
            'rol_destinatario' => 'Administrador',
            'titulo' => 'Solicitud Autorizada por Encargado',
            'mensaje' => "La solicitud #$id fue autorizada por el Encargado de departamento origen.",
            'link' => "/documentos/ver/$id"
        ]);

        // Registrar Seguimiento
        $this->seguimientoModel->registrar(
            $id,
            $_SESSION['usuario_id'],
            'SOLICITADO',
            'AUTORIZADO_ENCARGADO',
            'AUTORIZAR_ENCARGADO',
            "Autorizado por " . $_SESSION['rol_nombre'] . ". Pasa al Jefe de departamento destino."
        );

        header('Location: /documentos/ver/' . $id);
        exit;
    }

    /**
     * Autorizar documento — Paso 2: Jefe de departamento destino (AUTORIZADO_ENCARGADO → AUTORIZADO_DEPARTAMENTO/AUTORIZADO)
     */
    public function autorizar_depto($id) {
        
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        $isAdmin = (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false);
        
        // Validar que pertenezca al departamento DESTINO del jefe
        if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe')) {
            // Regla: Auto-aprobación prohibida
            if ($documento['usuario_id'] == $_SESSION['usuario_id']) {
                $_SESSION['error'] = 'Auto-aprobación denegada: Un Jefe de departamento no puede aprobar su propia solicitud.';
                header('Location: /documentos/ver/' . $id);
                exit;
            }

            $autorizado = false;
            if (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_destino_id'])) {
                if ($_SESSION['departamento_id'] == $documento['departamento_destino_id']) {
                    $autorizado = true;
                }
            } else {
                $deptoAsignado = !empty($documento['depto_destino_nombre']) ? $documento['depto_destino_nombre'] : $documento['depto_solicitante'];
                if ($deptoAsignado === $_SESSION['departamento']) {
                    $autorizado = true;
                }
            }
            
            if (!$autorizado) {
                $_SESSION['error'] = 'El documento no pertenece a su departamento destino';
                header('Location: /documentos');
                exit;
            }
        } elseif (!$isAdmin) {
            $_SESSION['error'] = 'No tiene permisos para realizar esta acción';
            header('Location: /documentos');
            exit;
        }
        
        if ($documento['estado'] !== 'AUTORIZADO_ENCARGADO') {
            $_SESSION['error'] = 'El documento debe ser autorizado por el Encargado de departamento primero (estado: AUTORIZADO_ENCARGADO esperado)';
            header('Location: /documentos/ver/' . $id);
            exit;
        }
        
        // Determinar tipo de flujo
        $tipoNombre = $documento['tipo_solicitud_nombre'] ?? $documento['tipo'] ?? '';
        $flujo = self::obtenerFlujo($tipoNombre);

        if ($flujo === 'B') {
            // Flujo B: La aprobación del Jefe destino es FINAL
            $this->documentoModel->actualizarEstado($id, 'AUTORIZADO');
            
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'AUTORIZAR_DEPTO_FINAL',
                "Documento autorizado (Aprobación Final Flujo B) por " . $_SESSION['rol_nombre'] . ": $id"
            );
            
            $_SESSION['success'] = 'Documento autorizado con éxito. El proceso ha finalizado (Flujo B).';
            
            // Notificar al creador de la solicitud
            $this->notificacionModel->crear([
                'usuario_id' => $documento['usuario_id'],
                'titulo' => 'Solicitud Autorizada — Proceso Finalizado (Flujo B)',
                'mensaje' => "Su solicitud #$id ha sido autorizada y el proceso ha finalizado.",
                'link' => "/documentos/ver/$id"
            ]);
            
            // Registrar Seguimiento
            $this->seguimientoModel->registrar(
                $id, 
                $_SESSION['usuario_id'], 
                'AUTORIZADO_ENCARGADO', 
                'AUTORIZADO', 
                'AUTORIZACION_FINAL_B', 
                "Autorizado y finalizado por " . $_SESSION['rol_nombre'] . " (Flujo B)."
            );
        } else {
            // Flujo A: Avanza a AUTORIZADO_DEPARTAMENTO (próximo: revisión Compras)
            $this->documentoModel->actualizarEstado($id, 'AUTORIZADO_DEPARTAMENTO');
            
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'AUTORIZAR_DEPTO',
                "Documento autorizado por " . $_SESSION['rol_nombre'] . ": $id"
            );
            
            $_SESSION['success'] = 'Documento autorizado departamentalmente con éxito (Flujo A). Pasa a revisión intermedia.';
            
            // Notificar a Jefes de departamento para revisión intermedia
            $this->notificacionModel->crear([
                'rol_destinatario' => 'documentos_autorizar_jefe',
                'titulo' => 'Documento Listo para Revisión Intermedia',
                'mensaje' => "El documento #$id fue autorizado por el Jefe de departamento de destino y está listo para revisión intermedia.",
                'link' => "/documentos/ver/$id"
            ]);
            
            // Registrar Seguimiento
            $this->seguimientoModel->registrar(
                $id, 
                $_SESSION['usuario_id'], 
                'AUTORIZADO_ENCARGADO', 
                'AUTORIZADO_DEPARTAMENTO', 
                'AUTORIZACION_DEPTO', 
                "Autorizado por " . $_SESSION['rol_nombre'] . ". Pasa a revisión intermedia (Flujo A)."
            );
        }
        
        header('Location: /documentos/ver/' . $id);
        exit;
    }
    
    /**
     * Aprobar documento (Firma / Aprobación Intermedia)
     */
    public function aprobar($id) {
        
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        $isAdmin = (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false);
        
        if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') && !$isAdmin) {
            $_SESSION['error'] = 'No está autorizado para realizar esta acción';
            header('Location: /documentos');
            exit;
        }
        
        if ($documento['estado'] !== 'AUTORIZADO_DEPARTAMENTO') {
            $_SESSION['error'] = 'El documento debe estar autorizado por el Jefe de departamento de destino primero.';
            header('Location: /documentos');
            exit;
        }
        
        // Firmar documento
        try {
            $config = $this->configModel->obtenerTodas();
            
            // Determinar rutas base con fallback
            $basePath = $config['path_originales'] ?? '';
            if (empty($basePath) || !is_dir($basePath)) {
                $basePath = STORAGE_PATH . '/documentos/';
            }
            
            $firmadosPath = $config['path_firmados'] ?? '';
            if (empty($firmadosPath) || !is_dir($firmadosPath)) {
                $firmadosPath = STORAGE_PATH . '/documentos/';
            }
            
            $rutaOriginal = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $documento['ruta_original'];
            $nombreFirmado = 'firmado_' . $documento['id'] . '_' . time() . '.json';
            $rutaFirmado = rtrim($firmadosPath, '/\\') . DIRECTORY_SEPARATOR . $nombreFirmado;
            
            if (!file_exists($rutaOriginal)) {
                throw new \Exception("El archivo original no se encontró en: $rutaOriginal");
            }

            // Cargar firma del usuario actual si existe
            $usuarioFirmante = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
            if (!empty($usuarioFirmante['firma_digital'])) {
                $rutaFirmaUser = STORAGE_PATH . '/usuarios_firmas/' . $usuarioFirmante['firma_digital'];
                if (file_exists($rutaFirmaUser)) {
                    $this->firmaDigital->setRutaLlavePrivada($rutaFirmaUser);
                }
            }

            if (!$this->firmaDigital->firmarDocumento($rutaOriginal, $rutaFirmado)) {
                throw new \Exception("La firma digital falló.");
            }
            
            // Actualizar estado
            if (!$this->documentoModel->actualizarEstado($id, 'APROBADO_COMPRAS', $nombreFirmado)) {
                throw new \Exception("No se pudo actualizar el estado en la base de datos.");
            }
            
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'APROBAR_DOCUMENTO',
                "Documento aprobado e intermedio firmado por " . $_SESSION['rol_nombre'] . ": $id"
            );
            
            $_SESSION['success'] = 'Documento aprobado y firmado intermedio exitosamente';

            // Notificar al solicitante
            $this->notificacionModel->crear([
                'usuario_id' => $documento['usuario_id'],
                'titulo' => 'Documento Aprobado Intermedio',
                'mensaje' => "Su documento #$id ha sido aprobado en revisión intermedia y está pendiente de autorización final.",
                'link' => "/documentos/ver/$id"
            ]);
            // Notificar a Gerencia
            $this->notificacionModel->crear([
                'rol_destinatario' => 'documentos_autorizar_gerencia',
                'titulo' => 'Documento Pendiente de Autorización',
                'mensaje' => "El documento #$id ha sido aprobado en revisión intermedia y requiere su autorización final.",
                'link' => "/documentos/ver/$id"
            ]);
            // Notificar a Administrador
            $this->notificacionModel->crear([
                'rol_destinatario' => 'Administrador',
                'titulo' => 'Documento Aprobado Intermedio',
                'mensaje' => "El documento #$id ha sido aprobado en revisión intermedia.",
                'link' => "/documentos/ver/$id"
            ]);

            // Registrar Seguimiento
            $this->seguimientoModel->registrar(
                $id, 
                $_SESSION['usuario_id'], 
                'AUTORIZADO_DEPARTAMENTO', 
                'APROBADO_COMPRAS', 
                'APROBACION_COMPRAS', 
                "Documento aprobado e intermedio firmado digitalmente por " . $_SESSION['rol_nombre'] . "."
            );

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error al procesar la aprobación: ' . $e->getMessage();
        }
        
        header('Location: /documentos/ver/' . $id);
        exit;
    }
    
    /**
     * Autorizar documento (Gerencia / Administrador)
     */
    public function autorizar($id) {
        
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        $isAdmin = (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false);
        
        if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia') && !$isAdmin) {
            $_SESSION['error'] = 'No está autorizado para realizar esta acción';
            header('Location: /documentos');
            exit;
        }
        
        if ($documento['estado'] !== 'APROBADO_COMPRAS') {
            $_SESSION['error'] = 'El documento no está en estado APROBADO_COMPRAS';
            header('Location: /documentos');
            exit;
        }
        
        // Actualizar estado
        $this->documentoModel->actualizarEstado($id, 'AUTORIZADO');
        
        $this->bitacoraModel->registrar(
            $_SESSION['usuario_id'],
            'AUTORIZAR_DOCUMENTO',
            "Documento autorizado final por " . $_SESSION['rol_nombre'] . ": $id"
        );
        
        $_SESSION['success'] = 'Documento autorizado exitosamente';

        // Notificar al solicitante
        $this->notificacionModel->crear([
            'usuario_id_destinatario' => $documento['usuario_id'],
            'titulo' => 'Documento Autorizado',
            'mensaje' => "Su documento #$id ha sido autorizado y el proceso ha finalizado.",
            'link' => "/documentos/ver/$id"
        ]);
        // Notificar a Administrador
        $this->notificacionModel->crear([
            'rol_destinatario' => 'Administrador',
            'titulo' => 'Documento Autorizado',
            'mensaje' => "El documento #$id ha sido autorizado por " . $_SESSION['rol_nombre'] . ".",
            'link' => "/documentos/ver/$id"
        ]);

        // Registrar Seguimiento
        $this->seguimientoModel->registrar(
            $id, 
            $_SESSION['usuario_id'], 
            'APROBADO_COMPRAS', 
            'AUTORIZADO', 
            'AUTORIZACION_FINAL', 
            "Documento autorizado por " . $_SESSION['rol_nombre'] . ". Proceso finalizado."
        );
        
        header('Location: /documentos/ver/' . $id);
        exit;
    }
    
    /**
     * Rechazar documento (Cualquier Jefe o Gerencia o Administrador)
     */
    public function rechazar($id) {
        
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        $isAdmin = (isset($_SESSION['rol_nombre']) && strpos(strtolower($_SESSION['rol_nombre']), 'admin') !== false);
        
        if (!\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe') && !\App\Controllers\AuthController::tienePermiso('documentos_autorizar_gerencia') && !\App\Controllers\AuthController::tienePermiso('documentos_autorizar_encargado') && !$isAdmin) {
            $_SESSION['error'] = 'No está autorizado para realizar esta acción';
            header('Location: /documentos');
            exit;
        }

        $comentario = $_POST['comentario'] ?? '';
        
        // Bajo el nuevo reglamento: Cualquier Jefe o Gerencia o Administrador rechaza y el ciclo termina de inmediato
        $this->documentoModel->rechazarConComentario($id, $comentario);
        $accion = 'RECHAZAR_DOCUMENTO';
        $mensaje = "Documento rechazado por " . $_SESSION['rol_nombre'] . ": $id. Motivo: $comentario";
        $_SESSION['success'] = 'Documento rechazado exitosamente';

        // Notificar al solicitante (Jefe de departamento origen)
        $this->notificacionModel->crear([
            'usuario_id' => $documento['usuario_id'],
            'titulo' => 'Documento Rechazado',
            'mensaje' => "Su solicitud #$id ha sido rechazada por " . $_SESSION['rol_nombre'] . " (" . ($_SESSION['departamento'] ?? 'N/A') . "). Motivo: $comentario",
            'link' => "/documentos/ver/$id"
        ]);
        
        $this->bitacoraModel->registrar($_SESSION['usuario_id'], $accion, $mensaje);
        
        // Registrar Seguimiento
        $this->seguimientoModel->registrar(
            $id, 
            $_SESSION['usuario_id'], 
            $documento['estado'], 
            'RECHAZADO', 
            $accion, 
            $mensaje
        );

        header('Location: /documentos/ver/' . $id);
        exit;
    }

    /**
     * Editar documento (Corrección por parte del solicitante)
     */
    public function editar($id) {
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }

        // El creador puede editar si está rechazado (tanto Secretaria como Jefe de departamento)
        $rolActual = $_SESSION['rol_nombre'] ?? '';
        $esCreadorHabilitado = (\App\Controllers\AuthController::tienePermiso('documentos_gestionar_propios') || \App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe'));
        $isAdmin = strpos(strtolower($rolActual), 'admin') !== false;

        if ($documento['usuario_id'] != $_SESSION['usuario_id'] || $documento['estado'] !== 'RECHAZADO') {
            if (!$isAdmin) {
                $_SESSION['error'] = 'No tiene permisos para editar este documento';
                header('Location: /documentos/ver/' . $id);
                exit;
            }
        }

        $deptoModel = new \App\Models\Departamento();
        $tipoSolicitudModel = new \App\Models\TipoSolicitud();
        
        $departamentos = $deptoModel->obtenerActivos();
        $tiposSolicitud = $tipoSolicitudModel->obtenerActivos();

        require_once VIEWS_PATH . '/documentos/editar.php';
    }

    /**
     * Procesar actualización del documento corregido
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /documentos');
            exit;
        }

        $documentoOriginal = $this->documentoModel->obtenerPorId($id);
        $isAdminActualizar = strpos(strtolower($_SESSION['rol_nombre'] ?? ''), 'admin') !== false;
        if (!$documentoOriginal || ($documentoOriginal['usuario_id'] != $_SESSION['usuario_id'] && !$isAdminActualizar) || $documentoOriginal['estado'] !== 'RECHAZADO') {
            $_SESSION['error'] = 'Acción no permitida';
            header('Location: /documentos');
            exit;
        }

        $descripcion = $_POST['descripcion'] ?? '';
        $tipo_solicitud_id = $_POST['tipo_solicitud_id'] ?? '';
        $departamento_destino_id = $_POST['departamento_destino_id'] ?? '';
        
        if (empty($descripcion) || empty($tipo_solicitud_id) || empty($departamento_destino_id)) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            header("Location: /documentos/editar/$id");
            exit;
        }

        // Obtener el nombre del tipo de solicitud para mantener compatibilidad hacia atrás
        $tipoSolicitudModel = new \App\Models\TipoSolicitud();
        $tipoObj = $tipoSolicitudModel->obtenerPorId($tipo_solicitud_id);
        $tipo = $tipoObj ? $tipoObj['nombre'] : 'Otros';

        $nombreArchivo = $documentoOriginal['ruta_original']; // Mantener el mismo por defecto

        // Si sube un nuevo archivo
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['archivo'];
            $config = $this->configModel->obtenerTodas();
            $maxSizeMB = (int)($config['max_file_size'] ?? 100);
            
            if ($archivo['size'] <= ($maxSizeMB * 1024 * 1024)) {
                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                
                // Validar solo PDF
                if ($extension !== 'pdf') {
                    $_SESSION['error'] = 'Solo se permiten archivos PDF.';
                    header("Location: /documentos/editar/$id");
                    exit;
                }
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $archivo['tmp_name']);
                finfo_close($finfo);

                if ($mime !== 'application/pdf') {
                    $_SESSION['error'] = 'El archivo no es un PDF válido.';
                    header("Location: /documentos/editar/$id");
                    exit;
                }
                $nombreArchivo = $id . '_' . time() . '.' . $extension;
                $rutaDestino = STORAGE_PATH . '/documentos/' . $nombreArchivo;
                move_uploaded_file($archivo['tmp_name'], $rutaDestino);
            }
        }

        $datos = [
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'ruta_original' => $nombreArchivo,
            'prioridad' => $_POST['prioridad'] ?? 'Normal',
            'tipo_solicitud_id' => $tipo_solicitud_id,
            'departamento_destino_id' => $departamento_destino_id
        ];

        if ($this->documentoModel->actualizar($id, $datos)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CORREGIR_DOCUMENTO',
                "Documento corregido y re-enviado: $id"
            );
            $_SESSION['success'] = 'Documento corregido y enviado a revisión';

            // Notificar a Compras y Administrador que el documento ha sido corregido y reenviado
            $this->notificacionModel->crear([
                'rol_destinatario' => 'documentos_autorizar_jefe',
                'departamento' => 'Compras',
                'titulo' => 'Documento Corregido y Reenviado',
                'mensaje' => "El documento #$id ha sido corregido por el solicitante y está listo para revisión.",
                'link' => "/documentos/ver/$id"
            ]);
            $this->notificacionModel->crear([
                'rol_destinatario' => 'Administrador',
                'titulo' => 'Documento Corregido',
                'mensaje' => "El documento #$id ha sido corregido y reenviado.",
                'link' => "/documentos/ver/$id"
            ]);

            // Registrar Seguimiento
            $this->seguimientoModel->registrar(
                $id, 
                $_SESSION['usuario_id'], 
                'RECHAZADO', 
                'SOLICITADO', 
                'CORRECCION', 
                "Documento corregido y re-enviado por el solicitante."
            );

            header('Location: /documentos/ver/' . $id);
        } else {
            $_SESSION['error'] = 'Error al actualizar el documento';
            header("Location: /documentos/editar/$id");
        }
        exit;
    }
    
    /**
     * Descargar documento
     */
    public function descargar($id, $tipo = 'original') {
        $this->servirArchivo($id, $tipo, 'attachment');
    }

    public function visualizar($id, $tipo = 'original') {
        $this->servirArchivo($id, $tipo, 'inline');
    }

    private function servirArchivo($id, $tipo, $disposition) {
        $documento = $this->documentoModel->obtenerPorId($id);
        
        if (!$documento) {
            $_SESSION['error'] = 'Documento no encontrado';
            header('Location: /documentos');
            exit;
        }
        
        // Verificar permisos
        if (\App\Controllers\AuthController::tienePermiso('documentos_autorizar_jefe')) {
            $esPropietario = ($documento['usuario_id'] == $_SESSION['usuario_id']);
            $perteneceOrigen = (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_origen_id']) && $_SESSION['departamento_id'] == $documento['departamento_origen_id']);
            $perteneceDestino = (!empty($_SESSION['departamento_id']) && !empty($documento['departamento_destino_id']) && $_SESSION['departamento_id'] == $documento['departamento_destino_id']);
            
            // Fallback por texto si no tienen IDs asignados
            $perteneceOrigenTexto = ($documento['depto_solicitante'] === $_SESSION['departamento']);
            $perteneceDestinoTexto = (($documento['depto_destino_nombre'] ?? $documento['depto_solicitante']) === $_SESSION['departamento']);

            if (!$esPropietario && !$perteneceOrigen && !$perteneceDestino && !$perteneceOrigenTexto && !$perteneceDestinoTexto) {
                $_SESSION['error'] = 'No tiene permisos para acceder a este documento';
                header('Location: /documentos');
                exit;
            }
        }
        
        $archivo = ($tipo === 'firmado' && $documento['ruta_firmado']) 
            ? $documento['ruta_firmado'] 
            : $documento['ruta_original'];
        
        $config = $this->configModel->obtenerTodas();
        if ($tipo === 'firmado') {
            $basePath = $config['path_firmados'] ?? (STORAGE_PATH . '/documentos/');
        } elseif ($documento['es_digitalizado'] == 1) {
            $basePath = $config['path_digitalizados'] ?? (STORAGE_PATH . '/digitized/');
        } else {
            $basePath = $config['path_originales'] ?? (STORAGE_PATH . '/documentos/');
        }

        $rutaArchivo = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $archivo;
        
        if (!file_exists($rutaArchivo)) {
            if ($disposition === 'inline') {
                header("HTTP/1.0 404 Not Found");
                ?>
                <!DOCTYPE html>
                <html>
                <body style="margin:0;display:flex;flex-direction:column;justify-content:center;align-items:center;height:100vh;background:#f8fafc;color:#64748b;font-family:system-ui,-apple-system,sans-serif;">
                    <svg height="64" width="64" viewBox="0 0 24 24" fill="#cbd5e1"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM10 17l-3.5-3.5 1.41-1.41L10 14.17 15.09 9.08 16.5 10.5 10 17z"/></svg>
                    <h3 style="margin:1rem 0 0.5rem;font-weight:700;color:#475569;">Archivo No Disponible</h3>
                    <p style="margin:0;font-size:12px;max-width:300px;text-align:center;">El documento solicitado no se encuentra en el almacenamiento físico del servidor.</p>
                </body>
                </html>
                <?php
                exit;
            }
            $_SESSION['error'] = 'Archivo físico no encontrado en el servidor';
            header('Location: /documentos');
            exit;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $rutaArchivo);
        finfo_close($finfo);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($archivo) . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        readfile($rutaArchivo);
        exit;
    }

    /**
     * Descargar archivo de soporte
     */
    public function descargar_soporte($id) {
        $this->servirArchivoSoporte($id, 'attachment');
    }

    /**
     * Visualizar archivo de soporte
     */
    public function visualizar_soporte($id) {
        $this->servirArchivoSoporte($id, 'inline');
    }

    private function servirArchivoSoporte($id, $disposition) {
        $adjunto = $this->adjuntoModel->obtenerPorId($id);
        
        if (!$adjunto) {
            $_SESSION['error'] = 'Adjunto no encontrado';
            header('Location: /documentos');
            exit;
        }

        $config = $this->configModel->obtenerTodas();
        $basePath = $config['path_soporte'] ?? '';
        if (empty($basePath) || !is_dir($basePath) || !is_writable($basePath)) {
            $basePath = STORAGE_PATH . '/documentos/';
        }

        $rutaArchivo = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $adjunto['nombre_archivo'];

        if (!file_exists($rutaArchivo)) {
            $_SESSION['error'] = 'Archivo físico de soporte no encontrado';
            header('Location: /documentos/ver/' . $adjunto['documento_id']);
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $rutaArchivo);
        finfo_close($finfo);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($adjunto['nombre_archivo']) . '"');
        header('Content-Length: ' . filesize($rutaArchivo));
        readfile($rutaArchivo);
        exit;
    }
    /**
     * Guardar documento digitalizado
     */
    public function guardar_digitalizado() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /documentos');
            exit;
        }

        $id = $_POST['id'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        if (empty($id) || empty($descripcion) || empty($tipo)) {
            $_SESSION['error'] = 'Todos los campos son requeridos';
            header('Location: /documentos/digitalizados');
            exit;
        }

        if ($this->documentoModel->existeId($id)) {
            $_SESSION['error'] = 'El ID del documento ya existe';
            header('Location: /documentos/digitalizados');
            exit;
        }

        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Debe seleccionar un archivo escaneado';
            header('Location: /documentos/digitalizados');
            exit;
        }

        $archivo = $_FILES['archivo'];
        
        // Determinar ruta de guardado
        $config = $this->configModel->obtenerTodas();
        $basePath = $config['path_digitalizados'] ?? '';
        if (empty($basePath) || !is_dir($basePath)) {
            $basePath = STORAGE_PATH . '/digitized/';
        }
        
        if (!is_dir($basePath)) {
            @mkdir($basePath, 0755, true);
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if ($extension !== 'pdf') {
            $_SESSION['error'] = 'Solo se permiten archivos PDF.';
            header('Location: /documentos/digitalizados');
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            $_SESSION['error'] = 'El archivo no es un PDF válido.';
            header('Location: /documentos/digitalizados');
            exit;
        }
        $nombreArchivo = 'DIG_' . $id . '_' . time() . '.' . $extension;
        $rutaDestino = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $datos = [
                'id' => $id,
                'descripcion' => $descripcion,
                'tipo' => $tipo,
                'ruta_original' => $nombreArchivo,
                'usuario_id' => $_SESSION['usuario_id'],
                'es_digitalizado' => 1,
                'estado' => 'DIGITALIZADO'
            ];

            if ($this->documentoModel->crear($datos)) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'REGISTRO_DIGITALIZADO',
                    "Documento digitalizado archivado: $id"
                );

                // Registrar Seguimiento
                $this->seguimientoModel->registrar(
                    $id, 
                    $_SESSION['usuario_id'], 
                    null, 
                    'DIGITALIZADO', 
                    'REGISTRO_DIGITALIZADO', 
                    "Documento digitalizado archivado directamente."
                );

                $_SESSION['success'] = 'Documento digitalizado registrado correctamente';
                header('Location: /documentos');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar en la base de datos';
            }
        } else {
            $_SESSION['error'] = 'Error al subir el archivo al servidor';
        }

        header('Location: /documentos/digitalizados');
        exit;
    }

    /**
     * Vista exclusiva para documentos digitalizados (Módulo Único)
     */
    public function archivoDigital() {
        $_GET['estado'] = 'DIGITALIZADO';
        $this->listar();
    }
}

