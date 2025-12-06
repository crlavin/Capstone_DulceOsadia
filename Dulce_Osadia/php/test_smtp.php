<?php
// Mostrar todos los errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Prueba Final: Puerto 587 + IPv4 Forzado</h1>";

// 1. CARGAR CONFIGURACIÓN
// Usamos $_SERVER['DOCUMENT_ROOT'] para asegurar que encuentre los archivos sin importar la carpeta
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 2. EL TRUCO MÁGICO: FORZAR IPv4
// Render intenta usar IPv6 por defecto, pero Gmail a veces lo bloquea o la red falla.
// Al usar gethostbyname, obtenemos la IP numérica (v4) directa de Gmail.
$host_ipv4 = gethostbyname('smtp.gmail.com');

echo "<h3>Configuración que se usará:</h3>";
echo "<ul>";
echo "<li><strong>Host Original:</strong> smtp.gmail.com</li>";
echo "<li><strong>Host IPv4 (Resuelto):</strong> " . $host_ipv4 . "</li>";
echo "<li><strong>Puerto:</strong> " . MAIL_PORT . " (Debe ser 587)</li>";
echo "<li><strong>Usuario:</strong> " . MAIL_USER . "</li>";
echo "</ul>";

$mail = new PHPMailer(true);

try {
    // Activar log detallado
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    
    // USAMOS LA IP DIRECTAMENTE EN LUGAR DEL NOMBRE DE DOMINIO
    $mail->Host       = $host_ipv4; 
    
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    
    // CONFIGURACIÓN ESTÁNDAR PARA GMAIL (TLS)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587; 
    $mail->CharSet    = 'UTF-8';

    // OPCIONES EXTRA PARA EVITAR ERRORES SSL EN LA NUBE
    // Esto hace que PHPMailer sea menos estricto con los certificados de seguridad
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Enviar correo a ti mismo
    $mail->setFrom(MAIL_USER, 'Prueba Render Final');
    $mail->addAddress(MAIL_USER); 

    $mail->isHTML(true);
    $mail->Subject = '¡Funcionó! Puerto 587 + IPv4';
    $mail->Body    = '<h1>Conexión Exitosa</h1><p>Esta es la configuración correcta que debes usar.</p>';

    echo "<div style='background:#eee; padding:10px; border:1px solid #999; max-height:300px; overflow:auto;'>";
    $mail->send();
    echo "</div>";
    
    echo "<h2 style='color:green'>✅ ¡PROBLEMA RESUELTO!</h2>";
    echo "<p>Si ves este mensaje verde, copia la configuración de este archivo a tu <b>enviar_email.php</b>.</p>";

} catch (Exception $e) {
    echo "</div>";
    echo "<h2 style='color:red'>❌ Error Fatal</h2>";
    echo "Info: " . $mail->ErrorInfo;
}
?>