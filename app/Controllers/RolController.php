<?php
namespace App\Controllers;

use App\Models\Rol;
use App\Models\Bitacora;

class RolController {
    private $rolModel;
    private $bitacoraModel;

    public function __construct() {
        $this->rolModel     = new Rol();
        $this->bitacoraModel = new Bitacora();
    }

    /**
     * Listar todos los roles
     */
    public function index() {
        $roles = $this->rolModel->obtenerTodos();
        require_once VIEWS_PATH . '/admin/roles/index.php';
    }

    /**
     * Crear un nuevo rol
     */
    public function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /configuracion/roles');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre del rol no puede estar vacío.';
            header('Location: /configuracion/roles');
            exit;
        }

        if ($this->rolModel->crear($nombre)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ROL_CREADO',
                "Se creó el rol: $nombre"
            );
            $_SESSION['success'] = "Rol \"$nombre\" creado correctamente.";
        } else {
            $_SESSION['error'] = 'No se pudo crear el rol. Intente de nuevo.';
        }

        header('Location: /configuracion/roles');
        exit;
    }

    /**
     * Editar (renombrar) un rol existente
     */
    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /configuracion/roles');
            exit;
        }

        $id     = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if (!$id || empty($nombre)) {
            $_SESSION['error'] = 'Datos inválidos para actualizar el rol.';
            header('Location: /configuracion/roles');
            exit;
        }

        $rolActual = $this->rolModel->obtenerPorId($id);
        if (!$rolActual) {
            $_SESSION['error'] = 'El rol no existe.';
            header('Location: /configuracion/roles');
            exit;
        }

        if (strtolower($rolActual['nombre']) === 'administrador') {
            $_SESSION['error'] = 'El rol Administrador no puede ser renombrado.';
            header('Location: /configuracion/roles');
            exit;
        }

        if ($this->rolModel->actualizar($id, $nombre)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ROL_EDITADO',
                "Se renombró el rol \"{$rolActual['nombre']}\" a \"$nombre\""
            );
            $_SESSION['success'] = "Rol actualizado a \"$nombre\" correctamente.";
        } else {
            $_SESSION['error'] = 'No se pudo actualizar el rol. Intente de nuevo.';
        }

        header('Location: /configuracion/roles');
        exit;
    }

    /**
     * Eliminar un rol (con validaciones de seguridad)
     */
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /configuracion/roles');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);

        if (!$id) {
            $_SESSION['error'] = 'ID de rol inválido.';
            header('Location: /configuracion/roles');
            exit;
        }

        $rol = $this->rolModel->obtenerPorId($id);
        if (!$rol) {
            $_SESSION['error'] = 'El rol no existe.';
            header('Location: /configuracion/roles');
            exit;
        }

        if ($this->rolModel->eliminar($id)) {
            $this->bitacoraModel->registrar(
                $_SESSION['usuario_id'],
                'ROL_ELIMINADO',
                "Se eliminó el rol: {$rol['nombre']}"
            );
            $_SESSION['success'] = "Rol \"{$rol['nombre']}\" eliminado correctamente.";
        } else {
            $_SESSION['error'] = 'No se puede eliminar este rol. Puede estar protegido o tener usuarios asignados.';
        }

        header('Location: /configuracion/roles');
        exit;
    }
}
