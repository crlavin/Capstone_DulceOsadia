<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database();
$con = $db->conectar();

$productos_session = $_SESSION['carrito']['productos'] ?? null;
$lista_carrito = [];

if ($productos_session != null) {
    foreach ($productos_session as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id_producto, nombre_presentacion, precio_venta FROM presentaciones_venta WHERE id_producto=?");
        $sql->execute([$clave]);
        $row_producto = $sql->fetch(PDO::FETCH_ASSOC);

        if ($row_producto) {
            $row_producto['cantidad'] = $cantidad;
            $lista_carrito[] = $row_producto;
        }
    }
} else {
    header("Location: checkout.php");
    exit;
}

// Calculamos el total en CLP
$total_general_clp = 0;
foreach ($lista_carrito as $producto) {
    $total_general_clp += $producto['cantidad'] * $producto['precio_venta'];
}

$con = null;
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Dulce Osadia - Pagar</title>
    <meta name="description" content="Tienda en línea de dulce osadia.">
    <meta name="keywords" content="dulce osadia, tienda de chocolates">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .contenedor-items {
            margin-top: 20px;
        }

        .resumen-pago {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
        }

        table {
            width: 100%;
            max-width: 600px;
            /* Ajuste para que no sea tan ancha */
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 12px;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-row {
            font-weight: bold;
            font-size: 1.2em;
        }

        .payment-section {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main>
        <section class="contenedor">
            <div class="contenedor-items">

                <div class="payment-section">
                    <h1 style="text-align: center;">Detalles de Pago</h1>
                    <p>Serás redirigido al portal seguro de Webpay para completar tu pago.</p>

                    <form action="crear_transaccion.php" method="POST">
                        <button type="submit" style="background-color: #d82b2b; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 18px; cursor: pointer;">
                            Pagar con Webpay
                        </button>
                    </form>
                </div>
                <div class="resumen-pago">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista_carrito as $producto) :
                                $nombre = $producto['nombre_presentacion'];
                                $total = $producto['cantidad'] * $producto['precio_venta'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($nombre); ?></td>
                                    <td><?php echo MONEDA . number_format($total, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td>Total a Pagar</td>
                                <td><?php echo MONEDA . number_format($total_general_clp, 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>


            </div>
        </section>
    </main>

</body>
<?php include 'footer.php'; ?>

</html>