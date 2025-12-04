<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Incluir archivos de PHPMailer
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

// Asegurarnos de que las variables de la compra existen
if (!isset($response) || !isset($_SESSION['user_email'])) {
    echo "Error: No se encontraron los datos de la compra para enviar el correo.";
    return; // Usamos return en lugar de exit para no detener el script padre
}

$mail = new PHPMailer(true);

try {
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USER;
    $mail->Password = MAIL_PASS; // Ahora usará la contraseña de aplicación desde .env
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = MAIL_PORT;
    $mail->setLanguage('es'); // Configurar idioma para mensajes de error

    // Remitente (tu tienda)
    $mail->setFrom(MAIL_USER, 'Dulce Osadia');

    // Destinatarios
    $mail->addAddress($_SESSION['user_email']); // Correo para el cliente
    $mail->addBCC(MAIL_USER); // Copia oculta para ti (el vendedor)

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Confirmacion de tu compra en Dulce Osadia - Orden ' . $response->getBuyOrder();

    // Generar QR para retiro de compra
    $orden = $response->getBuyOrder();
    $monto = $response->getAmount();
    $fechaIso = date('c');

    // Payload del QR: JSON legible y fácil de validar al escanear
    $qrPayload = json_encode([
        'tienda' => 'Dulce Osadia',
        'mensaje' => 'Con este codigo QR puedes retirar tu compra',
        'orden' => $orden,
        'monto' => $monto,
        'fecha' => $fechaIso
    ], JSON_UNESCAPED_UNICODE);

    // URL del servicio de generación de QR (220x220)
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($qrPayload);

    // Intentar incrustar la imagen del QR como CID para mayor compatibilidad
    $qrImgHtml = '';
    $qrImageData = @file_get_contents($qrUrl);
    if ($qrImageData !== false) {
        $mail->addStringEmbeddedImage($qrImageData, 'qr_compra', 'qr.png', 'base64', 'image/png');
        $qrImgHtml = '<img src="cid:qr_compra" alt="Código QR para retiro" style="width:220px;height:220px;border:1px solid #ddd;border-radius:8px" />';
    } else {
        // Fallback: referencia externa si no se pudo descargar la imagen
        $qrImgHtml = '<img src="' . htmlspecialchars($qrUrl) . '" alt="Código QR para retiro" style="width:220px;height:220px;border:1px solid #ddd;border-radius:8px" />';
    }

    // Crear un cuerpo de correo más completo
    $cuerpo = '
        <html>
        <body>
            <h2>¡Gracias por tu compra, ' . htmlspecialchars($_SESSION['user_name']) . '!</h2>
            <p>Hemos recibido tu pedido y lo estamos preparando.</p>
            <hr>
            <h3>Detalles de la Compra:</h3>
            <ul>
                <li><strong>Folio de la orden:</strong> ' . $orden . '</li>
                <li><strong>Fecha:</strong> ' . date('d-m-Y H:i:s') . '</li>
                <li><strong>Total pagado:</strong> ' . MONEDA . number_format($monto, 0, ',', '.') . '</li>
                <li><strong>Metodo de pago:</strong> Webpay</li>
            </ul>
            <hr>
            <h3>Retiro en tienda</h3>
            <p>Con este código QR puedes retirar tu compra. Preséntalo en tienda (en tu teléfono o impreso).</p>
            <div style="margin:12px 0;">' . $qrImgHtml . '</div>
            <p style="font-size:12px;color:#555;">Si no ves el código, habilita la carga de imágenes o responde este correo.</p>
            <p>Saludos,<br>El equipo de Dulce Osadia</p>
        </body>
        </html>
    ';

    $mail->Body = $cuerpo;
    $mail->AltBody = 'Gracias por tu compra. Orden: ' . $orden . '. Presenta el siguiente código para retiro: ' . $qrPayload;

    $mail->send();
    // No mostramos un mensaje de éxito para no interferir con la redirección final

} catch (Exception $e) {
    // Si falla, podemos guardar el error en un log en lugar de mostrarlo en pantalla
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
    // echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}"; // Descomentar para depurar
}
