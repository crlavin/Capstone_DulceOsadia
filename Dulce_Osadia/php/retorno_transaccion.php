<?php
// Usamos rutas absolutas para evitar errores de "No such file"
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/database.php';

// Usamos las clases del SDK de Transbank
use Transbank\Webpay\Options;
use Transbank\Webpay\WebpayPlus\Transaction;

// 1. OBTENER EL TOKEN DE LA TRANSACCIÓN
$token = $_POST['token_ws'] ?? $_GET['token_ws'] ?? null;

// Si no hay token, fuera.
if (empty($token)) {
    header('Location: pago_fallido.php?error=cancelado');
    exit();
}

try {
    // 2. CONFIRMAR TRANSACCIÓN CON TRANSBANK
    $transaction = (WEBPAY_PLUS_INTEGRATION_TYPE === Options::ENVIRONMENT_PRODUCTION)
        ? Transaction::buildForProduction(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE)
        : Transaction::buildForIntegration(WEBPAY_PLUS_API_KEY, WEBPAY_PLUS_COMMERCE_CODE);
        
    $response = $transaction->commit($token);

    // 3. VERIFICAR SI FUE APROBADO
    if ($response->isApproved()) {

        $db = new Database();
        $con = $db->conectar();
        
        // Recuperar carrito
        $productos_en_carrito = $_SESSION['carrito']['productos'] ?? [];

        if (empty($productos_en_carrito)) {
            // Error: Pagó pero el carrito está vacío
            header('Location: pago_fallido.php?error=carrito_vacio');
            exit();
        }
        
        try {
            $con->beginTransaction();
            
            // --- CORRECCIÓN CLIENTE ---
            // Intentamos obtener el ID del cliente de la sesión
            $id_cliente = $_SESSION['user_id'] ?? null;
            $email_cliente = $_SESSION['user_email'] ?? 'invitado@dulceosadia.cl';

            // Si no tenemos ID en sesión, buscamos el ID en la BD usando el email
            if (!$id_cliente) {
                $sql_buscar_cliente = $con->prepare("SELECT id FROM clientes WHERE email = ? LIMIT 1");
                $sql_buscar_cliente->execute([$email_cliente]);
                $cliente_encontrado = $sql_buscar_cliente->fetch(PDO::FETCH_ASSOC);
                
                if ($cliente_encontrado) {
                    $id_cliente = $cliente_encontrado['id'];
                } else {
                    // Si no existe el cliente, ¡CRÍTICO! La tabla compra requiere un id_cliente válido.
                    // Opción de emergencia: Usar el cliente ID 1 o crear uno al vuelo.
                    // Por ahora, lanzamos error para que lo veas.
                    throw new Exception("No se pudo identificar al cliente para el email: $email_cliente");
                }
            }

            // 4. GUARDAR COMPRA
            $sql_compra = $con->prepare("INSERT INTO compra (id_transaccion, fecha, status, email, id_cliente, total) VALUES (?, NOW(), ?, ?, ?, ?)");
            // OJO: status 'COMPLETED' debe caber en tu columna varchar(50)
            $sql_compra->execute([ 
                $response->getBuyOrder(), 
                'COMPLETED', 
                $email_cliente, 
                $id_cliente, 
                $response->getAmount() 
            ]);
            
            $id_compra = $con->lastInsertId();

            // 5. GUARDAR DETALLES
            foreach ($productos_en_carrito as $id_producto => $cantidad) {
                // Buscamos datos del producto (presentaciones_venta)
                $sql_prod = $con->prepare("SELECT nombre_presentacion, precio_venta, stock FROM presentaciones_venta WHERE id_presentacion=?");
                // NOTA: Asumí que la clave del carrito es id_presentacion. Si es id_producto, cambia el WHERE.
                $sql_prod->execute([$id_producto]);
                $row_prod = $sql_prod->fetch(PDO::FETCH_ASSOC);
                
                if ($row_prod) {
                    $sql_detalle = $con->prepare("INSERT INTO detalle_compra (id_compra, id_producto, nombre, precio, cantidad) VALUES (?, ?, ?, ?, ?)");
                    // OJO AQUÍ: id_producto en detalle_compra requiere un ID válido.
                    // Si $id_producto es id_presentacion, asegúrate que coincida.
                    $sql_detalle->execute([
                        $id_compra, 
                        $id_producto, // Aquí asumimos que esto es lo que espera la tabla
                        $row_prod['nombre_presentacion'], 
                        $row_prod['precio_venta'], 
                        $cantidad
                    ]);
                    
                    // Actualizar stock
                    $sql_stock = $con->prepare("UPDATE presentaciones_venta SET stock = stock - ? WHERE id_presentacion = ?");
                    $sql_stock->execute([$cantidad, $id_producto]);
                }
            }
            
            $con->commit();
            
            // Limpiar carrito
            unset($_SESSION['carrito']);

            // Enviar correo (usando la ruta corregida automáticamente por Composer)
            // Asegúrate que enviar_email.php use require 'vendor/autoload.php'
            include 'enviar_email.php';

            header('Location: completado.php?key=' . $response->getBuyOrder());
            exit();

        } catch (PDOException $e) {
            $con->rollBack();
            // ¡AQUÍ ESTÁ EL CAMBIO! Te mostrará el error real en pantalla.
            die("<h1 style='color:red; text-align:center; margin-top:50px;'>❌ ERROR SQL: " . $e->getMessage() . "</h1>");
        } catch (Exception $e) {
            $con->rollBack();
            die("<h1 style='color:red; text-align:center; margin-top:50px;'>❌ ERROR LÓGICO: " . $e->getMessage() . "</h1>");
        }

    } else {
        // Pago rechazado
        header('Location: pago_fallido.php?error=rechazado');
        exit();
    }

} catch (Exception $e) {
    die("Error crítico de Transbank: " . $e->getMessage());
}
?>