<?php
namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Documento;
use App\Models\Bitacora;

class DashboardController {
    private $usuarioModel;
    private $documentoModel;
    private $bitacoraModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->documentoModel = new Documento();
        $this->bitacoraModel = new Bitacora();
    }
    
    /**
     * Mostrar dashboard principal
     */
    public function index() {
        // Carga de Dashboard especializado para digitalizadores
        if ($_SESSION['rol_nombre'] === 'Digitalizador') {
            $estadisticas = $this->documentoModel->obtenerEstadisticas($_SESSION['usuario_id']);
            $stats = ['SOLICITADO' => 0, 'APROBADO_COMPRAS' => 0, 'AUTORIZADO' => 0, 'RECHAZADO' => 0, 'DIGITALIZADO' => 0];
            foreach ($estadisticas as $stat) { $stats[$stat['estado']] = $stat['total']; }
            $documentosRecientes = array_slice($this->documentoModel->obtenerTodos(['usuario_id' => $_SESSION['usuario_id'], 'estado' => 'DIGITALIZADO']), 0, 10);
            require_once __DIR__ . '/../../views/dashboard/digitalizador.php';
            return;
        }

        // Dashboard Estándar/Admin
        $filtros = [];
        if (!AuthController::tienePermiso('documentos_ver')) {
            $filtros['usuario_id'] = $_SESSION['usuario_id'];
        }

        $estadisticas = $this->documentoModel->obtenerEstadisticas($filtros['usuario_id'] ?? null);
        $stats = ['SOLICITADO' => 0, 'APROBADO_COMPRAS' => 0, 'AUTORIZADO' => 0, 'RECHAZADO' => 0, 'DIGITALIZADO' => 0];
        foreach ($estadisticas as $stat) { $stats[$stat['estado']] = $stat['total']; }
        
        $documentosRecientes = array_slice($this->documentoModel->obtenerTodos($filtros), 0, 10);
        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
