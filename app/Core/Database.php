<?php
namespace App\Core;

use PDO;
use PDOException;

require_once __DIR__ . '/../../config.php';

class Database {
    private static $instance = null;
    private $connection;

    /** Segundos máximos para establecer la conexión */
    private const LOGIN_TIMEOUT = 10;

    /** Número de reintentos antes de renderizar error */
    private const MAX_RETRIES = 3;

    private function __construct() {
        // Garantizar que ningún error de conexión se muestre al usuario,
        // independientemente de la configuración display_errors del servidor.
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        // Parsear el host separado de opciones extra (ej: "ip;Encrypt=no;...")
        $hostRaw = DB_HOST;
        $hostParts = explode(';', $hostRaw, 2);
        $server    = trim($hostParts[0]);
        $extraOpts = isset($hostParts[1]) ? ';' . trim($hostParts[1]) : '';

        $dsn  = "sqlsrv:Server={$server};Database=" . DB_NAME
              . ";LoginTimeout=" . self::LOGIN_TIMEOUT
              . $extraOpts;
        $user = empty(DB_USER) ? null : DB_USER;
        $pass = empty(DB_PASS) ? null : DB_PASS;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $lastException = null;
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $this->connection = new PDO($dsn, $user, $pass, $options);
                return; // Conexión exitosa
            } catch (PDOException $e) {
                $lastException = $e;
                // Esperar 1 segundo entre reintentos (excepto en el último)
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                }
            }
        }

        // Registrar el error real en el log (nunca mostrarlo al usuario)
        $logDir = defined('BASE_PATH') ? BASE_PATH . '/storage/logs' : __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $timestamp  = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] DB_CONNECTION_ERROR: " . $lastException->getMessage() . PHP_EOL;
        @file_put_contents($logDir . '/db_errors.log', $logMessage, FILE_APPEND | LOCK_EX);

        // Mostrar página de error amigable sin exponer credenciales ni detalles técnicos
        if (!headers_sent()) {
            http_response_code(503);
        }
        $errorView = defined('BASE_PATH')
            ? BASE_PATH . '/views/errors/db_error.php'
            : __DIR__ . '/../../views/errors/db_error.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>Servicio no disponible</h1><p>No se pudo conectar a la base de datos. Por favor, intente más tarde.</p>';
        }
        exit;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevenir clonación
    private function __clone() {}

    // Prevenir deserialización
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
