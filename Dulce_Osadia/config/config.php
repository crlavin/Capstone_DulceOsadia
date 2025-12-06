<?php
/**
 * Archivo de Configuración Principal
 */

// 1. Iniciar sesión de forma segura
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Contador del carrito
$num_cart = 0;
if (isset($_SESSION['carrito']['productos']) && is_array($_SESSION['carrito']['productos'])) {
    $num_cart = array_sum($_SESSION['carrito']['productos']);
}

use Dotenv\Dotenv;

// 3. Cargar Composer
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// 4. Cargar variables locales (.env) si existen
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/data.env')) {
    $dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'], 'data.env');
    $dotenv->safeLoad();
}

// 5. Constantes Generales
define("CURRENCY", "CLP");
define("KEY_TOKEN", "ZXC.qwe-876**");
define("MONEDA", "$");
define("SITE_URL", "https://dulceosadia.onrender.com");

// 6. Configuración de Correo (Actualizado a Brevo)
// Si Render tiene variables, las usa. Si no, usa estos valores por defecto.
define("MAIL_HOST", getenv('MAIL_HOST') ?: "smtp-relay.brevo.com");
define("MAIL_USER", getenv('MAIL_USER') ?: "9d789c001@smtp-brevo.com"); // Se lee de Render
define("MAIL_PASS", getenv('MAIL_PASS') ?: "UWDa4QzwYMvB73sO"); // Se lee de Render
define("MAIL_PORT", getenv('MAIL_PORT') ?: "2525"); // Puerto seguro antibloqueo

// 7. Transbank (Integración)
define("WEBPAY_PLUS_COMMERCE_CODE", "597055555532");
define("WEBPAY_PLUS_API_KEY", "579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C");
define("WEBPAY_PLUS_INTEGRATION_TYPE", "TEST");

// 8. Zona Horaria
date_default_timezone_set('America/Santiago');
?>