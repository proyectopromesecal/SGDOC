<?php
namespace App\Controllers;

use App\Models\Departamento;
use App\Models\Bitacora;

class DepartamentoController {
    private $departamentoModel;
    private $bitacoraModel;
    
    public function __construct() {
        $this->departamentoModel = new Departamento();
        $this->bitacoraModel = new Bitacora();
    }
    
    /**
     * Listar todos los departamentos
     */
    public function index() {
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 10;
        
        $departamentos = $this->departamentoModel->obtenerTodos($pagina, $limite);
        $totalRegistros = $this->departamentoModel->contarTodos();
        $totalPaginas = ceil($totalRegistros / $limite);
        
        require_once VIEWS_PATH . '/departamentos/index.php';
    }
    
    /**
     * Guardar nuevo departamento
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /departamentos');
            exit;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre del departamento es requerido.';
            header('Location: /departamentos');
            exit;
        }
        
        // Verificar si ya existe
        if ($this->departamentoModel->existeNombre($nombre)) {
            $_SESSION['error'] = "El departamento '$nombre' ya está registrado.";
            header('Location: /departamentos');
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'activo' => 1
        ];
        
        if ($this->departamentoModel->crear($datos)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CREAR_DEPARTAMENTO',
                "Departamento creado: $nombre"
            );
            $_SESSION['success'] = 'Departamento creado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al crear el departamento en la base de datos.';
        }
        
        header('Location: /departamentos');
        exit;
    }
    
    /**
     * Actualizar departamento existente
     */
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /departamentos');
            exit;
        }
        
        $id = $_POST['id'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($id) || empty($nombre)) {
            $_SESSION['error'] = 'ID y nombre son obligatorios.';
            header('Location: /departamentos');
            exit;
        }
        
        // Verificar si ya existe
        if ($this->departamentoModel->existeNombre($nombre, $id)) {
            $_SESSION['error'] = "El departamento '$nombre' ya está registrado.";
            header('Location: /departamentos');
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ];
        
        if ($this->departamentoModel->actualizar($id, $datos)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ACTUALIZAR_DEPARTAMENTO',
                "Departamento ID $id actualizado a: $nombre"
            );
            $_SESSION['success'] = 'Departamento actualizado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al actualizar el departamento.';
        }
        
        header('Location: /departamentos');
        exit;
    }
    
    /**
     * Activar o desactivar departamento
     */
    public function cambiarEstado($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /departamentos');
            exit;
        }
        
        $departamento = $this->departamentoModel->obtenerPorId($id);
        if (!$departamento) {
            $_SESSION['error'] = 'Departamento no encontrado.';
            header('Location: /departamentos');
            exit;
        }
        
        $nuevoEstado = $departamento['activo'] == 1 ? 0 : 1;
        
        if ($this->departamentoModel->cambiarEstado($id, $nuevoEstado)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ESTADO_DEPARTAMENTO_CAMBIADO',
                "Se cambió el estado del departamento ID $id a " . ($nuevoEstado == 1 ? 'Activo' : 'Inactivo')
            );
            $_SESSION['success'] = 'Estado del departamento actualizado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al cambiar el estado del departamento.';
        }
        
        header('Location: /departamentos');
        exit;
    }

    /**
     * Guardar nuevo departamento vía AJAX
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
        
        if ($this->departamentoModel->existeNombre($nombre)) {
            echo json_encode(['success' => false, 'message' => "El departamento '$nombre' ya está registrado."]);
            exit;
        }
        
        $datos = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'activo' => 1
        ];
        
        if ($this->departamentoModel->crear($datos)) {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT id, nombre FROM departamentos WHERE nombre = '$nombre' ORDER BY id DESC");
            $nuevo = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'CREAR_DEPARTAMENTO',
                "Departamento creado (AJAX): $nombre"
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
