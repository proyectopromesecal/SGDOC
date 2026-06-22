<?php
namespace App\Services;

require_once __DIR__ . '/../Libs/PHPMailer/class.phpmailer.php';
require_once __DIR__ . '/../Libs/PHPMailer/class.smtp.php';

class MailService {
    private $mail;

    public function __construct() {
        $this->mail = new \PHPMailer();
        $this->mail->IsSMTP();
        $this->mail->CharSet = 'UTF-8';
        
        // Configuración desde variable de entorno
        $this->mail->Host = $_ENV['SMTP_HOST'] ?? 'localhost';
        $this->mail->Port = $_ENV['SMTP_PORT'] ?? 587;
        $this->mail->SMTPAuth = ($_ENV['SMTP_AUTH'] ?? 'true') === 'true';
        $this->mail->Username = $_ENV['SMTP_USER'] ?? '';
        $this->mail->Password = $_ENV['SMTP_PASS'] ?? '';
        $this->mail->SMTPSecure = ($_ENV['SMTP_PORT'] == 587) ? 'tls' : (($_ENV['SMTP_PORT'] == 465) ? 'ssl' : '');
        
        // Opciones para redes locales con problemas de certificados
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $this->mail->From = $_ENV['SMTP_USER'] ?? '';
        $this->mail->FromName = $_ENV['SMTP_FROM_NAME'] ?? 'SIGEDOC';
    }

    public function enviar($para, $asunto, $cuerpo) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($para);
            $this->mail->isHTML(true);
            $this->mail->Subject = $asunto;
            $this->mail->Body = $cuerpo;
            $this->mail->AltBody = strip_tags($cuerpo);

            return $this->mail->send();
        } catch (\Exception $e) {
            error_log("Error enviando correo: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
