<?php
namespace App\Controllers;

use App\Models\TipoSolicitud;
use App\Models\Bitacora;

class TipoSolicitudController {
    private $tipoSolicitudModel;
    private $bitacoraModel;
    
    public function __construct() {
        $this->tipoSolicitudModel = new TipoSolicitud();
        $this->bitacoraModel = new Bitacora();
    }
    
    /**
     * Listar todos los tipos de solicitud
     */
    public function index() {
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 10;
        
        $tipos = $this->tipoSolicitudModel->obtenerTodos($pagina, $limite);
        $totalRegistros = $this->tipoSolicitudModel->contarTodos();
        $totalPaginas = ceil($totalRegistros / $limite);
        
        require_once VIEWS_PATH . '/tipos_solicitudes/index.php';
    }
    
    /**
     * Guardar nuevo tipo de solicitud
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre del tipo de solicitud es requerido.';
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        // Verificar si ya existe
        if ($this->tipoSolicitudModel->existeNombre($nombre)) {
            $_SESSION['error'] = "El tipo de solicitud '$nombre' ya está registrado.";
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'activo' => 1
        ];
        
        if ($this->tipoSolicitudModel->crear($datos)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CREAR_TIPO_SOLICITUD',
                "Tipo de solicitud creado: $nombre"
            );
            $_SESSION['success'] = 'Tipo de solicitud creado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al crear el tipo de solicitud en la base de datos.';
        }
        
        header('Location: /tipos-solicitudes');
        exit;
    }
    
    /**
     * Actualizar tipo de solicitud existente
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $id = $_POST['id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($id) || empty($nombre)) {
            $_SESSION['error'] = 'ID y nombre son obligatorios.';
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        // Verificar si ya existe
        if ($this->tipoSolicitudModel->existeNombre($nombre, $id)) {
            $_SESSION['error'] = "El tipo de solicitud '$nombre' ya está registrado.";
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ];
        
        if ($this->tipoSolicitudModel->actualizar($id, $datos)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ACTUALIZAR_TIPO_SOLICITUD',
                "Tipo de solicitud ID $id actualizado a: $nombre"
            );
            $_SESSION['success'] = 'Tipo de solicitud actualizado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al actualizar el tipo de solicitud.';
        }
        
        header('Location: /tipos-solicitudes');
        exit;
    }
    
    /**
     * Activar o desactivar tipo de solicitud
     */
    public function cambiarEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $tipo = $this->tipoSolicitudModel->obtenerPorId($id);
        if (!$tipo) {
            $_SESSION['error'] = 'Tipo de solicitud no encontrado.';
            header('Location: /tipos-solicitudes');
            exit;
        }
        
        $nuevoEstado = $tipo['activo'] == 1 ? 0 : 1;
        
        if ($this->tipoSolicitudModel->cambiarEstado($id, $nuevoEstado)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ESTADO_TIPO_SOLICITUD_CAMBIADO',
                "Se cambió el estado del tipo de solicitud ID $id a " . ($nuevoEstado == 1 ? 'Activo' : 'Inactivo')
            );
            $_SESSION['success'] = 'Estado del tipo de solicitud actualizado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al cambiar el estado del tipo de solicitud.';
        }
        
        header('Location: /tipos-solicitudes');
        exit;
    }

    /**
     * Guardar nuevo tipo de solicitud vía AJAX
     */
    public function guardarAjax() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre es requerido.']);
            exit;
        }
        
        if ($this->tipoSolicitudModel->existeNombre($nombre)) {
            echo json_encode(['success' => false, 'message' => "El tipo de solicitud '$nombre' ya está registrado."]);
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'activo' => 1
        ];
        
        if ($this->tipoSolicitudModel->crear($datos)) {
            // SQLSRV PDO driver doesn't always support lastInsertId. We need to fetch it.
            // Or if we can just get it by name.
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT id, nombre FROM tipos_solicitudes WHERE nombre = '$nombre' ORDER BY id DESC");
            $nuevo = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CREAR_TIPO_SOLICITUD',
                "Tipo de solicitud creado (AJAX): $nombre"
            );
            
            echo json_encode([
                'success' => true, 
                'id' => $nuevo['id'] ?? null,
                'nombre' => $nombre,
                'message' => 'Creado exitosamente.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
        }
        exit;
    }
}
