<?php
namespace App\Controllers;

use App\Models\Configuracion;
use App\Models\Bitacora;

class ConfiguracionController {
    private $configModel;
    private $bitacoraModel;

    public function __construct() {
        $this->configModel = new Configuracion();
        $this->bitacoraModel = new Bitacora();
    }

    public function index() {
        $config = $this->configModel->obtenerTodas();
        require_once VIEWS_PATH . '/configuracion/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'path_originales' => $_POST['path_originales'] ?? '',
                'path_escaneos' => $_POST['path_escaneos'] ?? '',
                'path_soporte' => $_POST['path_soporte'] ?? '',
                'path_firmados' => $_POST['path_firmados'] ?? '',
                'max_file_size' => $_POST['max_file_size'] ?? '25',
                'allowed_extensions' => $_POST['allowed_extensions'] ?? ''
            ];

            if ($this->configModel->guardarMuchas($datos)) {
                $this->bitacoraModel->registrar(
                    $_SESSION['usuario_id'],
                    'CONFIGURACION_ACTUALIZADA',
                    'Se actualizó la configuración global del sistema'
                );
                $_SESSION['success'] = 'Configuración guardada correctamente.';
            } else {
                $_SESSION['error'] = 'Error al guardar la configuración.';
            }
        }
        header('Location: /configuracion');
        exit;
    }
}
