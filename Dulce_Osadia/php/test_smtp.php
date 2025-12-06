<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Prueba Definitiva (Hardcoded)</h1>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// --- DATOS DIRECTOS (SIN VARIABLES DE ENTORNO) ---
// Escribe aquí tus datos reales entre comillas
$usuario_brevo = 'dulceosadia02@gmail.com'; // Tu correo de login en Brevo
$clave_smtp    = 'PegarAquiTuClaveLargaDeBrevo'; // La clave que empieza con xsmtp...

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    // Usamos la IP directa para evitar problemas de DNS
    $mail->Host       = gethostbyname('smtp-relay.brevo.com'); 
    $mail->SMTPAuth   = true;
    
    // USAMOS LOS DATOS DIRECTOS
    $mail->Username   = $usuario_brevo;
    $mail->Password   = $clave_smtp;
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587; // Probemos 587 estándar primero
    $mail->CharSet    = 'UTF-8';

    // Opciones para evitar bloqueos SSL
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom($usuario_brevo, 'Prueba Final');
    $mail->addAddress($usuario_brevo); 

    $mail->isHTML(true);
    $mail->Subject = 'Si ves esto, tus claves son correctas';
    $mail->Body    = '<h1>¡Éxito!</h1><p>Las credenciales funcionan.</p>';

    $mail->send();
    echo "<h2 style='color:green'>✅ ¡FUNCIONÓ!</h2>";
    echo "<p>El problema era que Render no estaba leyendo bien las variables. Ahora ve a actualizar las variables en Render con estos mismos datos exactos.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Falló la autenticación</h2>";
    echo "<p>Si sigue diciendo 'Authentication failed', entonces:</p>";
    echo "<ul>";
    echo "<li>La clave SMTP no es correcta (genera una nueva en Brevo).</li>";
    echo "<li>El correo de usuario no es el que usaste para registrarte en Brevo.</li>";
    echo "<li>Tu cuenta de Brevo aún no ha sido activada para envíos (revisa tu email por si te pidieron validación).</li>";
    echo "</ul>";
}
?>