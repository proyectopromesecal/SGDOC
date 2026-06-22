<?php
namespace App\Controllers;

use App\Models\Nota;
use App\Controllers\AuthController;

class NotaController {
    private $notaModel;
    
    public function __construct() {
        $this->notaModel = new Nota();
    }
    
    public function index() {
        $pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        if ($pagina < 1) $pagina = 1;
        $limite = 10;
        
        $notas = $this->notaModel->obtenerTodas($pagina, $limite);
        $totalRegistros = $this->notaModel->contarTodas();
        $totalPaginas = ceil($totalRegistros / $limite);
        
        require_once __DIR__ . '/../../views/notas/index.php';
    }
    
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->notaModel->crear([
                'titulo' => $_POST['titulo'],
                'contenido' => $_POST['contenido'],
                'autor_id' => $_SESSION['usuario_id'],
                'color_tag' => $_POST['color_tag']
            ]);
        }
        header('Location: /notas');
        exit;
    }
}
