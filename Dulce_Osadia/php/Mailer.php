<?php
// Carga las clases de PHPMailer UNA SOLA VEZ al inicio del archivo.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once_once '../phpmailer/src/PHPMailer.php';
require_once_once '../phpmailer/src/SMTP.php';
require_once_once '../phpmailer/src/Exception.php';

class Mailer
{
    function enviarEmail($email, $asunto, $cuerpo)
    {
        // No es necesario el 'require_once config.php' aquí,
        // porque ya se cargó en el archivo 'registro.php' que llama a esta clase.

        $mail = new PHPMailer(true); // Habilitar excepciones es clave

        try {
            // Configuración del servidor (usa las constantes globales)
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8'; // Importante para tildes y ñ

            // Remitente y Destinatario
            $mail->setFrom(MAIL_USER, 'Dulce Osadia');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpo;
            $mail->setLanguage('es', '../phpmailer/language/phpmailer.lang-es.php'); // Corregí la ruta por si acaso

            // Si send() funciona, continúa. Si falla, lanza una excepción que será atrapada por el catch.
            $mail->send();
            return true; // Si llegó hasta aquí, el correo se envió.

        } catch (Exception $e) {
            // Si ocurre cualquier error, lo atrapamos aquí.
            // En un sitio real, no mostrarías el error al usuario. Lo guardarías en un log.
            // error_log("Mailer Error: " . $mail->ErrorInfo);
            return false; // Indicamos que hubo un fallo.
        }
    }
}