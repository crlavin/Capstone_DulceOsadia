<?php
/**
 * Archivo de Configuración Principal
 * Maneja sesiones, variables de entorno, constantes globales y configuración regional.
 */

// 1. Iniciar sesión de forma segura (si no está activa)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Calcular cantidad de productos en el carrito (para mostrar en el icono del menú)
$num_cart = 0;
if (isset($_SESSION['carrito']['productos']) && is_array($_SESSION['carrito']['productos'])) {
    $num_cart = array_sum($_SESSION['carrito']['productos']);
}

// Importar la clase Dotenv para leer archivos .env locales
use Dotenv\Dotenv;

// 3. Cargar el Autoloader de Composer
// Usamos $_SERVER['DOCUMENT_ROOT'] para garantizar que encuentre la carpeta 'vendor' 
// sin importar en qué subcarpeta se encuentre este archivo.
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// 4. Cargar variables de entorno (Lógica Híbrida Local/Nube)
// Si estamos en LOCAL y existe el archivo 'data.env', lo cargamos.
// Si estamos en RENDER, este archivo no existe, así que saltamos este paso y usamos las variables del sistema.
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/data.env')) {
    $dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'], 'data.env');
    $dotenv->safeLoad();
}

// 5. Definición de Constantes Generales
define("CURRENCY", "CLP");
define("KEY_TOKEN", "ZXC.qwe-876**"); // Clave para cifrar datos sensibles si lo necesitas
define("MONEDA", "$");
define("SITE_URL", "https://dulceosadia.onrender.com"); // URL base de tu sitio

// 6. Configuración de Correo (PHPMailer)
// 'getenv()' lee la variable ya sea del sistema (Render) o del archivo .env cargado.
// El operador '?:' asigna un valor por defecto si la variable no existe.
define("MAIL_HOST", getenv('MAIL_HOST') ?: "smtp.gmail.com");
define("MAIL_USER", getenv('MAIL_USER')); // Se lee de la variable de entorno
define("MAIL_PASS", getenv('MAIL_PASS')); // Se lee de la variable de entorno (Clave de Aplicación)
define("MAIL_PORT", getenv('MAIL_PORT') ?: "587");

// 7. Configuración de Transbank (Webpay Plus)
// Estas son las credenciales OFICIALES de INTEGRACIÓN (Pruebas) de Transbank.
// No las cambies hasta que Transbank te pida pasar a producción.
define("WEBPAY_PLUS_COMMERCE_CODE", "597055555532");
define("WEBPAY_PLUS_API_KEY", "579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C");
define("WEBPAY_PLUS_INTEGRATION_TYPE", "TEST"); // Cambiar a 'LIVE' solo en producción real

// 8. Configuración Regional (Hora de Chile)
date_default_timezone_set('America/Santiago');

?>