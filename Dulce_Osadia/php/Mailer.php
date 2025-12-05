<?php
// 1. CARGAR LIBRERÍAS CON COMPOSER (OBLIGATORIO)
// Usamos __DIR__ para encontrar la carpeta vendor en la raíz
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. PREPARAR EL CORREO
$mail = new PHPMailer(true);

try {
    // --- CONFIGURACIÓN DEL SERVIDOR ---
    // Usamos las constantes definidas en config/config.php
    // Asegúrate de tener estas constantes definidas o escribe los valores directos aquí para probar
    $mail->isSMTP();
    $mail->Host       = defined('MAIL_HOST') ? MAIL_HOST : 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = defined('MAIL_USER') ? MAIL_USER : 'dulceosadia02@gmail.com'; 
    $mail->Password   = defined('MAIL_PASS') ? MAIL_PASS : 'lwog shrm jrkr vigk'; // ¡Ojo! No uses tu clave normal, usa App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = defined('MAIL_PORT') ? MAIL_PORT : 587;
    $mail->CharSet    = 'UTF-8';

    // --- REMITENTE Y DESTINATARIO ---
    // Usamos las variables que vienen de retorno_transaccion.php
    $mail->setFrom($mail->Username, 'Dulce Osadía');
    $mail->addAddress($email_cliente); // $email_cliente existe porque este archivo se incluye dentro del flujo

    // --- CONTENIDO DEL CORREO ---
    $mail->isHTML(true);
    $mail->Subject = 'Confirmación de Compra #' . $response->getBuyOrder();
    
    // Cuerpo del mensaje (Simple)
    $cuerpo = "<h1>¡Gracias por tu compra!</h1>";
    $cuerpo .= "<p>Hola, hemos recibido tu pago correctamente.</p>";
    $cuerpo .= "<p><strong>Orden de Compra:</strong> " . $response->getBuyOrder() . "</p>";
    $cuerpo .= "<p><strong>Monto:</strong> $" . number_format($response->getAmount(), 0, ',', '.') . "</p>";
    $cuerpo .= "<p>Pronto prepararemos tu pedido.</p>";
    $cuerpo .= "<br><p>Atte, Equipo Dulce Osadía.</p>";

    $mail->Body = $cuerpo;
    $mail->AltBody = 'Gracias por tu compra. Orden #' . $response->getBuyOrder();

    // --- ENVIAR ---
    $mail->send();
    // (Opcional) Guardar log de éxito
    // error_log("Correo enviado correctamente a: " . $email_cliente);

} catch (Exception $e) {
    // Si falla el correo, NO detenemos la web, solo registramos el error silenciosamente
    // para que el cliente igual vea su pantalla de "Compra Exitosa"
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
}
?>