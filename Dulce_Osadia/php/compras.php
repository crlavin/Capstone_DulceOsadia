<?php

require 'config/database.php';
require 'config/config.php';
require 'clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$token = generarToken();
$_SESSION['token'] = $token;
$idCliente = $_SESSION['user_id'];

$sql = $con->prepare("SELECT id_transaccion, fecha, status, total FROM compra WHERE
id_cliente = ? ORDER BY DATE(fecha) DESC");
$sql->execute([$idCliente]);

?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

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
        html {
            height: 100%;
            /* Asegura que el html ocupe toda la altura */
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background-color: #fed794;
            background-image: url("../img/Patrones_rosados/Recurso_108.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }

        /* Esta es la regla clave. 
  Le dice al contenido principal que "crezca" y ocupe todo el espacio vertical disponible.
*/
        .main-content {
            /* O la clase/etiqueta que hayas usado para tu contenido principal */
            flex-grow: 1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
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

        .card {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-family: Arial, sans-serif;
            color: #333;
            padding-bottom: 10px;
        }

        .card-text {
            margin-bottom: 15px;
            font-size: 1rem;
            color: #6c757d;
        }

        button.ver-compra-btn {
            background-color: #A0C3D2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button.ver-compra-btn:hover {
            background-color: #A0C3D2;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main>
        <div class="container">
            <h2>Mis Compras</h2>

            <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>

                <div class="card">
                    <div class="card-body">
                        <p class="card-title"><strong>Fecha: </strong> <?php echo $row['fecha']; ?></p>
                        <p class="card-title"><strong>Folio: </strong><?php echo $row['id_transaccion']; ?></p>
                        <p class="card-title"><strong>Total: </strong><?php echo MONEDA . number_format($row['total'], 2, ',', '.');; ?></p>
                        <br><button onclick="location.href='compra_detalle.php?orden=<?php echo $row['id_transaccion']; ?>&token=<?php echo $token; ?>'" class="ver-compra-btn">Ver Compra</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>


</html>