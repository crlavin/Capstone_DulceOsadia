<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 1. VALIDACIÓN DE DATOS
$destinatario = $_SESSION['user_email'] ?? $email_cliente ?? null;
// Si no hay nombre en sesión, usamos 'Cliente' por defecto
$nombreCliente = $_SESSION['user_name'] ?? 'Amante del Chocolate';

if (empty($destinatario) || !isset($response)) {
    error_log("❌ Error Mailer: Faltan datos para enviar el correo.");
    return;
}

$mail = new PHPMailer(true);

try {
    // 2. CONFIGURACIÓN TÉCNICA (Brevo + Puerto 2525)
    $mail->isSMTP();
    // Usamos gethostbyname para conexión rápida en Render
    $mail->Host       = gethostbyname('smtp-relay.brevo.com');
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 2525;
    $mail->CharSet    = 'UTF-8';
    $mail->setLanguage('es');

    // Opciones SSL permisivas para evitar bloqueos de nube
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // 3. REMITENTE
    $mail->setFrom('dulceosadia02@gmail.com', 'Dulce Osadía');
    $mail->addAddress($destinatario, $nombreCliente);
    $mail->addBCC('dulceosadia02@gmail.com'); // Copia para ti

    // 4. PREPARAR DATOS
    $orden = $response->getBuyOrder();
    $monto = number_format($response->getAmount(), 0, ',', '.');
    $fecha = date('d/m/Y H:i');

    // 5. GENERAR QR (Alta Calidad)
    $qrData = json_encode([
        'orden' => $orden,
        'monto' => $response->getAmount(),
        'tienda' => 'Dulce Osadia'
    ]);
    // Usamos margin=1 para que el QR no quede pegado al borde y ecLevel=Q para mejor lectura
    $qrUrl = "https://quickchart.io/qr?text=" . urlencode($qrData) . "&size=300&ecLevel=Q&margin=1&dark=333333&light=ffffff";

    // 6. PLANTILLA HTML PROFESIONAL
    $mail->isHTML(true);
    $mail->Subject = "¡Tu pedido #$orden está confirmado! 🍫";

    $cuerpo = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
            .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
            .header { background-color: #d82b2b; color: #ffffff; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px; }
            .content { padding: 30px 25px; color: #333333; line-height: 1.6; }
            .welcome-text { font-size: 16px; color: #555; margin-bottom: 25px; text-align: center; }
            
            .order-box { background-color: #f9f9f9; border: 1px solid #eeeeee; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
            .order-row { display: flex; justify-content: space-between; border-bottom: 1px dashed #ddd; padding: 8px 0; font-size: 14px; }
            .order-row:last-child { border-bottom: none; }
            .order-total { font-size: 18px; font-weight: bold; color: #d82b2b; text-align: right; margin-top: 10px; }
            
            .qr-section { text-align: center; background-color: #fff0f0; border: 2px dashed #d82b2b; border-radius: 10px; padding: 20px; margin-top: 20px; }
            .qr-title { color: #d82b2b; font-weight: bold; font-size: 18px; margin-bottom: 10px; display: block; }
            .qr-img { width: 180px; height: 180px; border-radius: 8px; mix-blend-mode: multiply; }
            
            .footer { background-color: #333333; color: #aaaaaa; text-align: center; padding: 20px; font-size: 12px; }
            .btn { display: inline-block; background-color: #d82b2b; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 50px; font-weight: bold; margin-top: 20px; }
            
            /* Responsive */
            @media only screen and (max-width: 600px) {
                .email-container { width: 100% !important; margin: 0 !important; border-radius: 0 !important; }
                .content { padding: 20px; }
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <!-- Cabecera -->
            <div class='header'>
                <h1>¡Gracias por tu Dulce Osadía! 🍫</h1>
            </div>
            
            <!-- Contenido Principal -->
            <div class='content'>
                <p class='welcome-text'>Hola <strong>$nombreCliente</strong>,<br>¡Qué alegría! Hemos recibido tu pedido correctamente. Ya estamos poniendo manos a la obra para prepararlo.</p>
                
                <!-- Resumen -->
                <div class='order-box'>
                    <div class='order-row'>
                        <span>📄 N° Orden:</span>
                        <strong>$orden</strong>
                    </div>
                    <div class='order-row'>
                        <span>📅 Fecha:</span>
                        <strong>$fecha</strong>
                    </div>
                    <div class='order-row'>
                        <span>💳 Medio de Pago:</span>
                        <strong>Webpay Plus</strong>
                    </div>
                    <div class='order-total'>
                        Total: $$monto
                    </div>
                </div>

                <!-- Sección QR -->
                <div class='qr-section'>
                    <span class='qr-title'>📦 Ticket de Retiro</span>
                    <p style='margin: 5px 0 15px 0; font-size: 14px;'>Muestra este código al vendedor para retirar tu compra.</p>
                    <img src='$qrUrl' alt='Código QR de Retiro' class='qr-img'>
                    <p style='margin-top: 10px; font-size: 12px; color: #777;'>Código único: $orden</p>
                </div>

               <div style='text-align: center; margin-top: 30px;'>
                    <!-- Ponemos el estilo directamente en la etiqueta A -->
                    <a href='" . SITE_URL . "' target='_blank' style='display: inline-block; background-color: #d82b2b; color: #ffffff !important; text-decoration: none !important; padding: 14px 30px; border-radius: 50px; font-weight: bold; font-size: 16px; border: 1px solid #d82b2b;'>
                        <!-- Span interno por si el cliente de correo ignora el estilo del enlace -->
                        <span style='color: #ffffff !important; text-decoration: none;'>Volver a la Tienda</span>
                    </a>
                </div>
            </div>

            <!-- Pie de Página -->
            <div class='footer'>
                <p>&copy; " . date('Y') . " Dulce Osadía - Repostería Artesanal</p>
                <p>¿Tienes dudas? Responde a este correo y te ayudaremos.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body    = $cuerpo;
    // Texto plano alternativo por si el cliente de correo no soporta HTML
    $mail->AltBody = "Hola $nombreCliente. Gracias por tu compra #$orden por un total de $$monto. Presenta este número de orden para retirar tu pedido.";

    $mail->send();
} catch (Exception $e) {
    error_log("❌ ERROR MAILER: " . $mail->ErrorInfo);
}
