<?php

session_start();

$num_cart = 0;
if (isset($_SESSION['carrito']['productos']) && is_array($_SESSION['carrito']['productos'])) {
    // Mostrar total de unidades en el carrito, no solo productos distintos
    $num_cart = array_sum($_SESSION['carrito']['productos']);
}

use Dotenv\Dotenv;
// Carga el autoloader de Composer para que el SDK de Transbank funcione
require_once __DIR__ . '/../vendor/autoload.php';

// Carga variables de entorno desde el archivo data.env en la raíz del proyecto
$dotenv = Dotenv::createImmutable(dirname(__DIR__), 'data.env');
$dotenv->safeLoad();



define("CURRENCY", "CLP");
define("KEY_TOKEN", "ZXC.qwe-876**");
define("MONEDA", "$");
define("SITE_URL","http://localhost/Dulce_Osadia/php");
define("MAIL_HOST", "smtp.gmail.com");
define("MAIL_USER", $_ENV['MAIL_USER']);
define("MAIL_PASS", $_ENV['MAIL_PASS']);
define("MAIL_PORT", "587");
define("WEBPAY_PLUS_COMMERCE_CODE", "597055555532");
define("WEBPAY_PLUS_API_KEY", "579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C");
define("WEBPAY_PLUS_INTEGRATION_TYPE", "TEST");
?>