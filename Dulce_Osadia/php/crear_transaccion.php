<?php
require_once 'config/config.php';
require_once 'config/database.php';

// ✅ Usamos las mismas clases que en tu archivo de retorno
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

// 1. RECALCULAR EL TOTAL (Tu lógica aquí es perfecta y se mantiene)
$productos_en_carrito = $_SESSION['carrito']['productos'] ?? [];
if (empty($productos_en_carrito)) {
    die("Error: Tu carrito de compras está vacío.");
}

$db = new Database();
$con = $db->conectar();
$total_a_pagar = 0;
foreach ($productos_en_carrito as $clave => $cantidad) {
    $sql = $con->prepare("SELECT precio_venta FROM presentaciones_venta WHERE id_producto = ?");
    $sql->execute([$clave]);
    $producto_db = $sql->fetch(PDO::FETCH_ASSOC);
    if ($producto_db) {
        $total_a_pagar += $producto_db['precio_venta'] * $cantidad;
    }
}

// 2. PREPARAR DATOS PARA LA TRANSACCIÓN
$buy_order = 'DO-' . time();
$session_id = session_id();
$amount = $total_a_pagar;
$return_url = SITE_URL . '/retorno_transaccion.php';

try {
    // 3. CONFIGURAR Y CREAR LA TRANSACCIÓN (Usando la misma sintaxis que te funcionó)
    $transaction = (WEBPAY_PLUS_INTEGRATION_TYPE === Options::ENVIRONMENT_PRODUCTION)
        ? Transaction::buildForProduction(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE)
        : Transaction::buildForIntegration(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE);

    $response = $transaction->create($buy_order, $session_id, $amount, $return_url);

    // 4. REDIRIGIR AL USUARIO AL PORTAL DE PAGO
    if (isset($response->url) && isset($response->token)) {
        echo '
        <form id="webpay-form" action="' . htmlspecialchars($response->url) . '" method="POST">
            <input type="hidden" name="token_ws" value="' . htmlspecialchars($response->token) . '">
        </form>
        <script>document.getElementById("webpay-form").submit();</script>
        ';
    } else {
        die("Error: No se pudo crear la transacción en Transbank. Revisa tus credenciales.");
    }
} catch (Exception $e) {
    die('Error de comunicación con Transbank: ' . $e->getMessage());
}
?>