<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Prueba de Correo: Brevo SMTP</h1>";

// Cargar configuración
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h3>Configuración detectada:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . MAIL_HOST . " (Debe ser smtp-relay.brevo.com)</li>";
echo "<li><strong>Puerto:</strong> " . MAIL_PORT . " (Debe ser 587)</li>";
echo "<li><strong>Usuario:</strong> " . MAIL_USER . "</li>";
echo "</ul>";

$mail = new PHPMailer(true);

try {
    // Configuración ESTÁNDAR (Sin trucos de IP)
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = MAIL_HOST; // Usamos el dominio normal
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = MAIL_PORT; 
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_USER, 'Prueba Brevo');
    $mail->addAddress(MAIL_USER); 

    $mail->isHTML(true);
    $mail->Subject = '¡Brevo funciona en Render!';
    $mail->Body    = '<h1>Éxito</h1><p>El sistema de correos está conectado correctamente.</p>';

    $mail->send();
    
    echo "<h2 style='color:green'>✅ ¡CORREO ENVIADO!</h2>";
    echo "<p>Ya puedes usar tu sistema de compras con confianza.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Error</h2>";
    echo "Info: " . $mail->ErrorInfo;
}
?>