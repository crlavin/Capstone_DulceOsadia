<?php
// Requerimos los archivos de configuración
require '../config/config.php';
require '../config/database.php';

// Usamos las clases del SDK de Transbank
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

// 1. OBTENER EL TOKEN DE LA TRANSACCIÓN
$token = $_POST['token_ws'] ?? $_GET['token_ws'] ?? null;

// Si no hay token (pago cancelado o error), redirigimos a una página de error amigable.
if (empty($token)) {
    header('Location: pago_fallido.php?error=cancelado');
    exit();
}

try {
    // 2. CONFIGURAR Y CONFIRMAR LA TRANSACCIÓN
    $transaction = (WEBPAY_PLUS_INTEGRATION_TYPE === Options::ENVIRONMENT_PRODUCTION)
        ? Transaction::buildForProduction(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE)
        : Transaction::buildForIntegration(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE);
        
    $response = $transaction->commit($token);

    // 3. VERIFICAR SI EL PAGO FUE APROBADO
    if ($response->isApproved()) {

        // --- INICIO DE LA LÓGICA DE GUARDADO EN BASE DE DATOS ---
        $db = new Database();
        $con = $db->conectar();
        
        $productos_en_carrito = $_SESSION['carrito']['productos'] ?? [];

        // Si el carrito está vacío en este punto, algo salió mal.
        if (empty($productos_en_carrito)) {
            // Es buena práctica registrar este error para investigarlo.
            error_log("Pago aprobado para la orden " . $response->getBuyOrder() . " pero el carrito estaba vacío.");
            header('Location: pago_fallido.php?error=carrito_vacio');
            exit();
        }
        
        try {
            $con->beginTransaction();
            
            // Guardar compra principal
            $sql_compra = $con->prepare("INSERT INTO compra (id_transaccion, fecha, status, email, id_cliente, total) VALUES (?, NOW(), ?, ?, ?, ?)");
            $sql_compra->execute([ $response->getBuyOrder(), 'COMPLETED', $_SESSION['user_email'] ?? 'correo@ejemplo.com', $_SESSION['user_id'] ?? null, $response->getAmount() ]);
            $id_compra = $con->lastInsertId();

            // Guardar detalles y actualizar stock
            foreach ($productos_en_carrito as $clave => $cantidad) {
                $sql_prod = $con->prepare("SELECT nombre_presentacion, precio_venta, stock FROM presentaciones_venta WHERE id_producto=?");
                $sql_prod->execute([$clave]);
                $row_prod = $sql_prod->fetch(PDO::FETCH_ASSOC);
                
                if ($row_prod) {
                    $sql_detalle = $con->prepare("INSERT INTO detalle_compra (id_compra, id_producto, nombre, precio, cantidad) VALUES (?, ?, ?, ?, ?)");
                    $sql_detalle->execute([$id_compra, $clave, $row_prod['nombre_presentacion'], $row_prod['precio_venta'], $cantidad]);
                    
                    // Lógica para actualizar el stock
                    $sql_stock = $con->prepare("UPDATE presentaciones_venta SET stock = stock - ? WHERE id_producto = ?");
                    $sql_stock->execute([$cantidad, $clave]);
                }
            }
            
            $con->commit();
            
            // Limpiar el carrito
            unset($_SESSION['carrito']);

            // Incluir el script para enviar el correo de confirmación
            include 'enviar_email.php';

            // Redirección inmediata a la página de éxito
            header('Location: completado.php?key=' . $response->getBuyOrder());
            exit();

        } catch (PDOException $e) {
            $con->rollBack();
            // En un entorno de producción, registraríamos el error en lugar de mostrarlo.
            error_log("Error de BD al guardar la compra: " . $e->getMessage());
            die("Hubo un error al procesar tu compra. Por favor, contacta a soporte.");
        }

    } else {
        // El pago fue rechazado por Transbank
        header('Location: pago_fallido.php?error=rechazado');
        exit();
    }

} catch (Exception $e) {
    // Error crítico al comunicarse con Transbank
    error_log("Error crítico de Transbank: " . $e->getMessage());
    die("Hubo un error de comunicación con el sistema de pagos. Por favor, intenta de nuevo más tarde.");
}
?>