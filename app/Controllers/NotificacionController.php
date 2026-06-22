<?php
namespace App\Controllers;

use App\Models\Notificacion;

class NotificacionController {
    private $notificacionModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->notificacionModel = new Notificacion();
    }

    // Endpoint JSON para obtener notificaciones (AJAX)
    public function obtenerRecientes() {
        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        $usuarioId = $_SESSION['usuario_id'];
        $rolNombre = $_SESSION['rol_nombre'];
        $departamento = $_SESSION['departamento'] ?? null;

        $notificaciones = $this->notificacionModel->obtenerPorUsuario($usuarioId, $rolNombre, $departamento);
        $noLeidas = $this->notificacionModel->contarNoLeidas($usuarioId, $rolNombre, $departamento);

        // Formatear fechas para "hace X minutos"
        foreach ($notificaciones as &$notif) {
            $notif['tiempo_transcurrido'] = $this->timeAgo($notif['fecha_creacion']);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'notificaciones' => $notificaciones,
            'no_leidas' => $noLeidas
        ]);
    }

    // Endpoint para marcar como leída
    public function marcarLeida($id) {
        if (!isset($_SESSION['usuario_id'])) {
            return;
        }
        $this->notificacionModel->marcarComoLeida($id, $_SESSION['usuario_id']);
        echo json_encode(['success' => true]);
    }
    
    // Endpoint para marcar todas
    public function marcarTodas() {
        if (!isset($_SESSION['usuario_id'])) {
            return;
        }
        $departamento = $_SESSION['departamento'] ?? null;
        $this->notificacionModel->marcarTodasLeidas($_SESSION['usuario_id'], $_SESSION['rol_nombre'], $departamento);
        echo json_encode(['success' => true]);
    }

    // Helper tiempo
    private function timeAgo($fecha) {
        $timestamp = strtotime($fecha);
        $diferencia = time() - $timestamp;
        
        if ($diferencia < 60) return "Hace un momento";
        if ($diferencia < 3600) return "Hace " . floor($diferencia / 60) . " min";
        if ($diferencia < 86400) return "Hace " . floor($diferencia / 3600) . " h";
        return date('d/m/Y', $timestamp);
    }
}
