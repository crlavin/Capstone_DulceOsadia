<?php

require '../config/database.php';
require '../config/config.php';
// require 'clienteFunciones.php'; // Asegúrate de que este archivo inicie la sesión

// Es crucial que la sesión ya esté iniciada antes de este punto
if (empty($_SESSION['token'])) {
    header("Location: login.php"); // Si no hay sesión, no debería estar aquí
    exit;
}

$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if ($orden === null || $token === null || $token !== $token_session) {
    header("Location: compras.php");
    exit;
}

$db = new Database();
$con = $db->conectar();

$sqlCompra = $con->prepare("SELECT id, id_transaccion, fecha, total FROM compra WHERE id_transaccion = ? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);

// CORRECCIÓN: Verificar si la compra existe antes de continuar
if (!$rowCompra) {
    // Puedes crear una página de error más amigable
    die("Error: La orden de compra no fue encontrada.");
}

$idCompra = $rowCompra['id'];

$sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
$sqlDetalle->execute([$idCompra]);

?>

<head>
    <meta charset="utf-8">
    <title>Dulce Osadia</title>
    <meta name="description" content="Tienda en línea de chocolates.">
    <meta name="keywords" content="dulce osadia, tienda de chocolates">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            padding-bottom: 60px;
            background-color: #fed794;
            background-image: url("../img/Patrones_rosados/Recurso_108.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        h1,
        h2,
        h3 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        td {
            background-color: #fafafa;
        }
    </style>
</head>

<?php include 'menu.php'; ?>

<main>
    <div class="container">
        <h2>Detalle de la compra</h2>

        <div class="compra-info">
            <p><strong>Fecha: </strong><?php echo htmlspecialchars($rowCompra['fecha']); ?></p>
            <p><strong>Orden: </strong><?php echo htmlspecialchars($rowCompra['id_transaccion']); ?></p>
            <p><strong>Total: </strong><?php echo MONEDA . number_format($rowCompra['total'], 0, ',', '.'); ?></p>
        </div>

        <hr>

        <h3>Productos</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)) {
                    $precio = $row['precio'];
                    $cantidad = $row['cantidad'];
                    // CORRECCIÓN: Calculamos solo el subtotal de esta fila
                    $subtotal = $cantidad * $precio;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo MONEDA . number_format($precio, 0, ',', '.'); ?></td>
                        <td><?php echo $cantidad; ?></td>
                        <td><?php echo MONEDA . number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>

</html>