<?php
namespace App\Core;

class FirmaDigital {
    private $privateKeyPath;
    private $publicKeyPath;
    private $certificatePath;
    
    public function __construct() {
        $this->privateKeyPath = STORAGE_PATH . '/keys/private.key';
        $this->publicKeyPath = STORAGE_PATH . '/keys/public.key';
        $this->certificatePath = STORAGE_PATH . '/keys/certificate.crt';
    }

    /**
     * Establecer ruta de llave privada personalizada
     */
    public function setRutaLlavePrivada($path) {
        if (file_exists($path)) {
            $this->privateKeyPath = $path;
        }
    }
    
    /**
     * Generar par de llaves RSA
     */
    public function generarLlaves($passphrase = null) {
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "config" => 'C:/laragon/bin/php/php-8.1.10-Win32-vs16-x64/extras/ssl/openssl.cnf'
        ];
        
        // Generar par de llaves
        $res = openssl_pkey_new($config);
        
        if (!$res) {
            throw new \Exception("Error al generar llaves: " . openssl_error_string());
        }
        
        // Exportar llave privada
        if (!openssl_pkey_export($res, $privateKey, $passphrase, $config)) {
            throw new \Exception("Error al exportar llave privada: " . openssl_error_string());
        }
        
        if (empty($privateKey)) {
            throw new \Exception("La llave privada generada está vacía.");
        }
        
        // Exportar llave pública
        $publicKeyDetails = openssl_pkey_get_details($res);
        $publicKey = $publicKeyDetails['key'];
        
        // Guardar llaves
        $keysDir = dirname($this->privateKeyPath);
        if (!is_dir($keysDir)) {
            mkdir($keysDir, 0700, true);
        }
        
        if (file_put_contents($this->privateKeyPath, $privateKey) === false) {
             throw new \Exception("No se pudo escribir el archivo de llave privada.");
        }
        if (file_put_contents($this->publicKeyPath, $publicKey) === false) {
             throw new \Exception("No se pudo escribir el archivo de llave pública.");
        }
        
        chmod($this->privateKeyPath, 0600);
        chmod($this->publicKeyPath, 0644);
        
        return true;
    }
    
    /**
     * Firmar documento PDF
     */
    public function firmarDocumento($rutaDocumento, $rutaSalida, $passphrase = null) {
        if (!file_exists($rutaDocumento)) {
            throw new \Exception("El documento no existe: $rutaDocumento");
        }
        
        if (!file_exists($this->privateKeyPath)) {
            throw new \Exception("No se encontró la llave privada. Genere las llaves primero.");
        }
        
        // Leer el contenido del documento
        $contenido = file_get_contents($rutaDocumento);
        
        // Obtener la llave privada
        $privateKey = openssl_pkey_get_private(
            file_get_contents($this->privateKeyPath),
            $passphrase
        );
        
        if (!$privateKey) {
            throw new \Exception("Error al cargar la llave privada: " . openssl_error_string());
        }
        
        // Crear firma digital
        $firma = '';
        $resultado = openssl_sign($contenido, $firma, $privateKey, OPENSSL_ALGO_SHA256);
        
        if (!$resultado) {
            throw new \Exception("Error al firmar el documento: " . openssl_error_string());
        }
        
        // Crear documento firmado con metadatos
        $documentoFirmado = [
            'contenido' => base64_encode($contenido),
            'firma' => base64_encode($firma),
            'fecha_firma' => date('Y-m-d H:i:s'),
            'algoritmo' => 'SHA256withRSA',
            'documento_original' => basename($rutaDocumento)
        ];
        
        // Guardar documento firmado
        $directorioSalida = dirname($rutaSalida);
        if (!is_dir($directorioSalida)) {
            mkdir($directorioSalida, 0755, true);
        }
        
        file_put_contents($rutaSalida, json_encode($documentoFirmado, JSON_PRETTY_PRINT));
        
        return true;
    }
    
    /**
     * Verificar firma digital
     */
    public function verificarFirma($rutaDocumentoFirmado) {
        if (!file_exists($rutaDocumentoFirmado)) {
            throw new \Exception("El documento firmado no existe");
        }
        
        if (!file_exists($this->publicKeyPath)) {
            throw new \Exception("No se encontró la llave pública");
        }
        
        // Leer documento firmado
        $documentoFirmado = json_decode(file_get_contents($rutaDocumentoFirmado), true);
        
        if (!$documentoFirmado) {
            throw new \Exception("Formato de documento firmado inválido");
        }
        
        // Decodificar contenido y firma
        $contenido = base64_decode($documentoFirmado['contenido']);
        $firma = base64_decode($documentoFirmado['firma']);
        
        // Obtener llave pública
        $publicKey = openssl_pkey_get_public(file_get_contents($this->publicKeyPath));
        
        if (!$publicKey) {
            throw new \Exception("Error al cargar la llave pública: " . openssl_error_string());
        }
        
        // Verificar firma
        $resultado = openssl_verify($contenido, $firma, $publicKey, OPENSSL_ALGO_SHA256);
        
        if ($resultado === 1) {
            return [
                'valido' => true,
                'fecha_firma' => $documentoFirmado['fecha_firma'],
                'algoritmo' => $documentoFirmado['algoritmo'],
                'documento_original' => $documentoFirmado['documento_original']
            ];
        } elseif ($resultado === 0) {
            return ['valido' => false, 'mensaje' => 'Firma inválida'];
        } else {
            throw new \Exception("Error al verificar la firma: " . openssl_error_string());
        }
    }
    
    /**
     * Extraer documento original de un documento firmado
     */
    public function extraerDocumentoOriginal($rutaDocumentoFirmado, $rutaSalida) {
        $documentoFirmado = json_decode(file_get_contents($rutaDocumentoFirmado), true);
        
        if (!$documentoFirmado) {
            throw new \Exception("Formato de documento firmado inválido");
        }
        
        $contenido = base64_decode($documentoFirmado['contenido']);
        
        $directorioSalida = dirname($rutaSalida);
        if (!is_dir($directorioSalida)) {
            mkdir($directorioSalida, 0755, true);
        }
        
        file_put_contents($rutaSalida, $contenido);
        
        return true;
    }
}
