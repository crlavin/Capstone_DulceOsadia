<?php
// Habilitar visualización de errores al máximo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Diagnóstico de Correo SMTP</h1>";

// 1. Cargar Configuración y Librerías
// Usamos $_SERVER['DOCUMENT_ROOT'] para ir a la segura con las rutas
$root = $_SERVER['DOCUMENT_ROOT'];

if (file_exists($root . '/config/config.php')) {
    require_once $root . '/config/config.php';
    echo "<p>✅ Configuración cargada.</p>";
} else {
    die("<p>❌ Error: No se encuentra config/config.php</p>");
}

if (file_exists($root . '/vendor/autoload.php')) {
    require_once $root . '/vendor/autoload.php';
    echo "<p>✅ Librerías Composer cargadas.</p>";
} else {
    die("<p>❌ Error: No se encuentra vendor/autoload.php (Composer no instalado o ruta mal)</p>");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 2. Verificar Variables de Entorno (Sin mostrar la clave real)
echo "<h3>1. Verificando Credenciales:</h3>";
echo "<ul>";
echo "<li><strong>HOST:</strong> " . MAIL_HOST . "</li>";
echo "<li><strong>PORT:</strong> " . MAIL_PORT . "</li>";
echo "<li><strong>USER:</strong> " . MAIL_USER . "</li>";
$passLen = strlen(MAIL_PASS);
echo "<li><strong>PASS:</strong> " . ($passLen > 0 ? "Cargada ($passLen caracteres)" : "<span style='color:red'>VACÍA (Revisa Render Environment)</span>") . "</li>";
echo "</ul>";

// 3. Prueba de Envío
echo "<h3>2. Intentando conectar con Gmail...</h3>";
echo "<div style='background:#f4f4f4; padding:15px; border:1px solid #ddd; font-family:monospace; font-size:12px; max-height:400px; overflow:auto;'>";

$mail = new PHPMailer(true);

try {
    // Activamos el modo DEBUG Nivel 2 (Muestra cliente y servidor)
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = 'html'; // Formato amigable para el navegador

    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';

    // Nos enviamos el correo a nosotros mismos
    $mail->setFrom(MAIL_USER, 'Diagnóstico Render');
    $mail->addAddress(MAIL_USER); // Enviar al mismo correo de la cuenta

    $mail->isHTML(true);
    $mail->Subject = 'Prueba de Conexión Exitosa - ' . date('H:i:s');
    $mail->Body    = '<h1>¡Funciona!</h1><p>Si lees esto, tu servidor puede enviar correos.</p>';

    $mail->send();
    echo "</div>"; // Fin del log
    echo "<h2 style='color:green'>✅ ¡ÉXITO! El correo se envió correctamente.</h2>";
    echo "<p>Revisa tu bandeja de entrada (y Spam).</p>";

} catch (Exception $e) {
    echo "</div>"; // Fin del log
    echo "<h2 style='color:red'>❌ ERROR FATAL</h2>";
    echo "<p>El correo no se pudo enviar. Revisa el log de arriba para ver la causa.</p>";
    echo "<p><strong>Error PHPMailer:</strong> " . $mail->ErrorInfo . "</p>";
}
?>
```

### 2. Sube el archivo y Ejecútalo

1.  Guarda el archivo en tu carpeta del proyecto.
2.  Sube los cambios a GitHub:
    ```bash
    git add test_smtp.php
    git commit -m "Agregar script de diagnostico SMTP"
    git push origin main