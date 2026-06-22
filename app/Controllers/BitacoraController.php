<?php
namespace App\Controllers;

use App\Models\Bitacora;
use App\Models\Documento;

class BitacoraController {
    private $bitacoraModel;

    public function __construct() {
        $this->bitacoraModel = new Bitacora();
    }

    /**
     * Mostrar listado de bitácora
     */
    public function index() {
        $filtros = [];
        if (!empty($_GET['usuario_id'])) $filtros['usuario_id'] = $_GET['usuario_id'];
        if (!empty($_GET['accion'])) $filtros['accion'] = $_GET['accion'];
        if (!empty($_GET['fecha_inicio'])) $filtros['fecha_inicio'] = $_GET['fecha_inicio'] . ' 00:00:00';
        if (!empty($_GET['fecha_fin'])) $filtros['fecha_fin'] = $_GET['fecha_fin'] . ' 23:59:59';
        
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 100;
        
        $registros = $this->bitacoraModel->obtenerRegistros($filtros, $pagina, $limite);
        $totalRegistros = $this->bitacoraModel->contarRegistros($filtros);
        $totalPaginas = ceil($totalRegistros / $limite);
        
        require_once VIEWS_PATH . '/bitacora/index.php';
    }
}
