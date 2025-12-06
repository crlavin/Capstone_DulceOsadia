<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 1. VALIDACIÓN
$destinatario = $_SESSION['user_email'] ?? $email_cliente ?? null;
$nombreCliente = $_SESSION['user_name'] ?? 'Cliente';

if (empty($destinatario) || !isset($response)) {
    error_log("❌ Error Mailer: Faltan datos.");
    return;
}

$mail = new PHPMailer(true);

try {
    // 2. CONFIGURACIÓN TÉCNICA (Puerto 2525 + Brevo)
    $mail->isSMTP();
    
    // IP Directa para velocidad y estabilidad en Render
    $mail->Host       = gethostbyname('smtp-relay.brevo.com');
    
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER; // El usuario técnico (9d789c001...)
    $mail->Password   = MAIL_PASS; // La clave API
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 2525;      // Puerto desbloqueado
    $mail->CharSet    = 'UTF-8';
    $mail->setLanguage('es');

    // Permisos SSL para nube
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // 3. REMITENTE (Estética)
    // Aquí ponemos tu correo real para que el cliente vea algo bonito
    $mail->setFrom('dulceosadia02@gmail.com', 'Dulce Osadía');
    
    $mail->addAddress($destinatario, $nombreCliente);
    $mail->addBCC('dulceosadia02@gmail.com'); // Copia oculta para ti

    // 4. DATOS
    $orden = $response->getBuyOrder();
    $monto = number_format($response->getAmount(), 0, ',', '.');
    $fecha = date('d-m-Y H:i');

    // 5. QR
    $qrData = json_encode(['orden' => $orden, 'monto' => $response->getAmount(), 'tienda' => 'Dulce Osadia']);
    $qrUrl = "https://quickchart.io/qr?text=" . urlencode($qrData) . "&size=300&ecLevel=H&margin=1";

    // 6. HTML
    $mail->isHTML(true);
    $mail->Subject = "Confirmación de compra #$orden";

    $cuerpo = "
    <div style='font-family: sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; border-radius: 8px;'>
        <div style='background: #d82b2b; color: white; padding: 20px; text-align: center;'>
            <h2 style='margin:0;'>¡Pedido Confirmado!</h2>
        </div>
        <div style='padding: 20px;'>
            <p>Hola <strong>$nombreCliente</strong>,</p>
            <p>Tu compra ha sido exitosa.</p>
            <div style='background:#f9f9f9; padding:10px; margin:10px 0; border-left:4px solid #d82b2b;'>
                <strong>Orden:</strong> $orden <br>
                <strong>Total:</strong> $$monto
            </div>
            <div style='text-align: center; margin: 20px;'>
                <p>Código de retiro:</p>
                <img src='$qrUrl' alt='QR' style='width: 150px;'>
            </div>
        </div>
    </div>";

    $mail->Body = $cuerpo;
    $mail->AltBody = "Compra exitosa. Orden: $orden. Total: $$monto";

    $mail->send();

} catch (Exception $e) {
    error_log("❌ ERROR MAILER: " . $mail->ErrorInfo);
}
?>