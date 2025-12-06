<?php
// 1. Cargar Configuración PRIMERO (Antes de cualquier echo para evitar error de sesión)
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/config/config.php';
require_once $root . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Habilitar reporte de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🕵️ Diagnóstico SMTP V2 (Modo Seguro)</h1>";

// --- TRUCO TÉCNICO: FORZAR IPv4 ---
// Obtenemos la IP numérica de Gmail para evitar que el servidor intente usar IPv6 y falle.
$host_ipv4 = gethostbyname(MAIL_HOST); 

echo "<h3>Configuración de Red:</h3>";
echo "<ul>";
echo "<li><strong>Host Original:</strong> " . MAIL_HOST . "</li>";
echo "<li><strong>IP Resuelta (IPv4):</strong> " . $host_ipv4 . " (Si esto es una IP, el truco funciona)</li>";
echo "<li><strong>Puerto:</strong> " . MAIL_PORT . " (Debe ser 465)</li>";
echo "</ul>";

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    
    // USAMOS LA IP DIRECTAMENTE
    $mail->Host       = $host_ipv4; 
    
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    
    // CAMBIO IMPORTANTE: Para puerto 465 usamos SMTPS (SSL Implícito)
    if (MAIL_PORT == 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';

    // Opciones extra para evitar errores de certificado SSL en servidores estrictos
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom(MAIL_USER, 'Diagnóstico V2');
    $mail->addAddress(MAIL_USER); 

    $mail->isHTML(true);
    $mail->Subject = 'Prueba Puerto 465 Exitoso';
    $mail->Body    = '<h1>¡Conexión establecida!</h1><p>El puerto 465 con IPv4 forzada funcionó.</p>';

    echo "<div style='background:#eee; padding:10px; border:1px solid #999; max-height:300px; overflow:auto;'>";
    $mail->send();
    echo "</div>";
    
    echo "<h2 style='color:green'>✅ ¡PROBLEMA RESUELTO!</h2>";

} catch (Exception $e) {
    echo "</div>";
    echo "<h2 style='color:red'>❌ Error Fatal</h2>";
    echo "Info: " . $mail->ErrorInfo;
}
?>
```

Sube este archivo y ejecútalo. Si ves el mensaje verde ✅, ve al paso 3.

### Paso 3: Aplicar el arreglo a `enviar_email.php`

Si el test funcionó, debes aplicar la misma lógica a tu archivo real de envío de correos.

Edita **`enviar_email.php`** y modifica la sección de configuración así:

```php
    // ... dentro del try { ...

    $mail->isSMTP();
    
    // TRUCO: Forzamos la resolución a IPv4 para evitar "Network Unreachable"
    $mail->Host = gethostbyname(MAIL_HOST); 
    
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    
    // Ajuste automático de encriptación según el puerto
    if (MAIL_PORT == 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Para puerto 465 (SSL)
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Para puerto 587 (TLS)
    }
    
    $mail->Port       = MAIL_PORT;
    
    // Parche SSL para servidores Docker
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // ... resto del código ...