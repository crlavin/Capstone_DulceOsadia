<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. VALIDACIÓN DE SEGURIDAD Y DATOS
// Intentamos obtener el correo de la sesión, o usamos la variable $email_cliente del archivo padre.
$destinatario = $_SESSION['user_email'] ?? $email_cliente ?? null;
$nombreCliente = $_SESSION['user_name'] ?? 'Cliente';

// Si no hay destinatario o no hay datos de la transacción, guardamos error y salimos.
if (empty($destinatario) || !isset($response)) {
    error_log("❌ Error Mailer: No se puede enviar correo. Faltan datos (Email o Response).");
    return; // Salimos del script sin romper la página
}

$mail = new PHPMailer(true);

try {
    // 2. CONFIGURACIÓN DEL SERVIDOR SMTP
    // $mail->SMTPDebug = 2; // Descomentar solo para depuración en pantalla (cuidado en producción)
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setLanguage('es');

    // 3. REMITENTE Y DESTINATARIO
    $mail->setFrom(MAIL_USER, 'Dulce Osadía');
    $mail->addAddress($destinatario, $nombreCliente);
    
    // Opcional: Copia oculta para el administrador
    // $mail->addBCC(MAIL_USER); 

    // 4. DATOS DE LA COMPRA
    $orden = $response->getBuyOrder();
    $monto = number_format($response->getAmount(), 0, ',', '.');
    $fecha = date('d-m-Y H:i');

    // 5. GENERACIÓN DEL CÓDIGO QR (Vía QuickChart API)
    // Creamos una URL de imagen directa. Es más compatible con Gmail/Outlook que adjuntar archivos.
    $qrData = json_encode([
        'orden' => $orden,
        'monto' => $response->getAmount(),
        'fecha' => date('c'),
        'tienda' => 'Dulce Osadia'
    ]);
    
    // URL segura para la imagen del QR
    $qrUrl = "https://quickchart.io/qr?text=" . urlencode($qrData) . "&size=300&ecLevel=H&margin=1";

    // 6. CONTENIDO DEL CORREO (HTML)
    $mail->isHTML(true);
    $mail->Subject = "Confirmación de compra #$orden - Dulce Osadía";

    $cuerpo = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
            .header { background-color: #d82b2b; padding: 20px; text-align: center; color: white; }
            .content { padding: 30px; background-color: #ffffff; }
            .details { background-color: #f9f9f9; border-left: 4px solid #d82b2b; padding: 15px; margin: 20px 0; }
            .qr-container { text-align: center; margin-top: 30px; }
            .qr-img { width: 200px; height: 200px; border: 1px solid #ddd; padding: 5px; border-radius: 5px; }
            .footer { background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin:0; font-size: 24px;'>¡Gracias por tu compra!</h1>
            </div>
            
            <div class='content'>
                <p>Hola <strong>$nombreCliente</strong>,</p>
                <p>Hemos recibido tu pago exitosamente. Aquí tienes el resumen de tu pedido:</p>
                
                <div class='details'>
                    <p><strong>N° Orden:</strong> $orden</p>
                    <p><strong>Fecha:</strong> $fecha</p>
                    <p><strong>Total:</strong> $$monto</p>
                </div>

                <div class='qr-container'>
                    <p><strong>Muestra este código QR para retirar en tienda:</strong></p>
                    <img src='$qrUrl' alt='Código QR de Retiro' class='qr-img'>
                    <p style='font-size: 12px; color: #999;'>Código único de retiro</p>
                </div>
            </div>

            <div class='footer'>
                <p>Dulce Osadía - Repostería Artesanal</p>
                <p>Si tienes dudas, responde a este correo.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body    = $cuerpo;
    $mail->AltBody = "Gracias por tu compra. Orden: $orden. Total: $$monto. Presenta este número de orden para retirar.";

    // 7. ENVIAR
    $mail->send();
    // error_log("✅ Correo enviado a $destinatario");

} catch (Exception $e) {
    // Si falla, guardamos el error en el log pero NO detenemos la ejecución
    error_log("❌ ERROR CRÍTICO MAILER: " . $mail->ErrorInfo);
}
?>