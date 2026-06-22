<?php
namespace App\Controllers;

use App\Models\Seguimiento;
use App\Models\Bitacora;

class SeguimientoController {
    private $seguimientoModel;
    private $bitacoraModel;
    
    public function __construct() {
        $this->seguimientoModel = new Seguimiento();
        $this->bitacoraModel = new Bitacora();
    }
    
    /**
     * Vista general de seguimiento (Trazabilidad)
     */
    public function index() {
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 150;
        
        $trazas = $this->seguimientoModel->obtenerRecientesPaginated($pagina, $limite);
        $totalRegistros = $this->seguimientoModel->contarTodos();
        $totalPaginas = ceil($totalRegistros / $limite);
        
        require_once VIEWS_PATH . '/seguimiento/index.php';
    }
}
