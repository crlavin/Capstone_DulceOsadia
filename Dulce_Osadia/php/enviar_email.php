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

    // Crear un cuerpo de correo más completo
    $cuerpo = '
        <html>
        <body>
            <h2>¡Gracias por tu compra, ' . htmlspecialchars($_SESSION['user_name']) . '!</h2>
            <p>Hemos recibido tu pedido y lo estamos preparando.</p>
            <hr>
            <h3>Detalles de la Compra:</h3>
            <ul>
                <li><strong>Folio de la orden:</strong> ' . $response->getBuyOrder() . '</li>
                <li><strong>Fecha:</strong> ' . date('d-m-Y H:i:s') . '</li>
                <li><strong>Total pagado:</strong> ' . MONEDA . number_format($response->getAmount(), 0, ',', '.') . '</li>
                <li><strong>Metodo de pago:</strong> Webpay</li>
            </ul>
            <p>Puedes ver el detalle de tus compras en tu cuenta.</p>
            <p>Saludos,<br>El equipo de Dulce Osadia</p>
        </body>
        </html>
    ';

    $mail->Body = $cuerpo;
    $mail->AltBody = 'Gracias por tu compra. El ID de tu orden es ' . $response->getBuyOrder() . '.';

    $mail->send();
    // No mostramos un mensaje de éxito para no interferir con la redirección final

} catch (Exception $e) {
    // Si falla, podemos guardar el error en un log en lugar de mostrarlo en pantalla
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
    // echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}"; // Descomentar para depurar
}
