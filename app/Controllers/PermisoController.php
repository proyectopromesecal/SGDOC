<?php
namespace App\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Bitacora;

class PermisoController {
    private $rolModel;
    private $permisoModel;
    private $bitacoraModel;

    public function __construct() {

        $this->rolModel = new Rol();
        $this->permisoModel = new Permiso();
        $this->bitacoraModel = new Bitacora();
    }

    /**
     * Mostrar la matriz de permisos
     */
    public function index() {
        $roles = $this->rolModel->obtenerTodos();
        $permisosAgrupados = $this->permisoModel->obtenerTodosAgrupados();
        
        // Obtener permisos asignados por cada rol
        $matriz = [];
        foreach ($roles as $rol) {
            $matriz[$rol['id']] = $this->permisoModel->obtenerIdsPorRol($rol['id']);
        }

        require_once VIEWS_PATH . '/admin/permisos.php';
    }

    /**
     * Guardar cambios en la matriz de permisos
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permisos_post = $_POST['permisos'] ?? []; // Array [rol_id => [permiso_id, ...]]
            $roles = $this->rolModel->obtenerTodos();
            
            $success = true;
            foreach ($roles as $rol) {
                // Protección: El rol Administrador siempre debe tener todos los permisos para evitar bloqueos
                if ($rol['nombre'] === 'Administrador') {
                    $totalPermisos = $this->permisoModel->obtenerTodosPlanos(); // Nuevo método necesario
                    $ids = array_column($totalPermisos, 'id');
                } else {
                    $ids = $permisos_post[$rol['id']] ?? [];
                }

                if (!$this->permisoModel->actualizarPermisosRol($rol['id'], $ids)) {
                    $success = false;
                }
            }

            if ($success) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'MATRIZ_PERMISOS_ACTUALIZADA',
                    'Se actualizó la matriz de permisos global'
                );
                $_SESSION['success'] = 'Matriz de permisos actualizada correctamente. Los cambios se aplicarán en el próximo inicio de sesión.';
            } else {
                $_SESSION['error'] = 'Ocurrió un error al actualizar algunos permisos.';
            }
        }
        header('Location: /configuracion/permisos');
        exit;
    }
}
