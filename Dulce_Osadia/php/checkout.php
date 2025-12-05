<?php
// 1. REQUERIR CONFIGURACIÓN
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

$productos_en_session = $_SESSION['carrito']['productos'] ?? null;
$lista_carrito = [];
$total_general = 0;

if ($productos_en_session != null) {
    foreach ($productos_en_session as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id_producto, nombre_presentacion, img, precio_venta FROM presentaciones_venta WHERE id_producto=?");
        $sql->execute([$clave]);
        $producto_db = $sql->fetch(PDO::FETCH_ASSOC);

        if ($producto_db) {
            $producto_db['cantidad'] = $cantidad;
            $lista_carrito[] = $producto_db;
        }
    }
}
$con = null;
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

        /* Contenedor visual similar al de las tarjetas del index */
        .tarjetas-container {
            width: 95%;
            max-width: 1200px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.92);
            padding: 24px 28px;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        }

        .tarjetas-container h1,
        .tarjetas-container h2 {
            color: #3a2a16;
            text-align: center;
            margin: 0 0 16px;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        /* Esta es la regla clave. 
 Le dice al contenido principal que "crezca" y ocupe todo el espacio vertical disponible.
*/
        .main-content {
            /* O la clase/etiqueta que hayas usado para tu contenido principal */
            flex-grow: 1;
        }

        .contenedor-items {
            margin-top: 20px;
            /* Ajusta el margen superior según sea necesario */
        }

        table {
            width: 100%; /* Ajustado de 200% a 100% para que quepa en el contenedor */
            border-collapse: collapse;
            margin-bottom: 20px;
            /* Espacio inferior entre la tabla y el botón */
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: center;
            padding: 8px;
            vertical-align: middle; /* Asegura alineación vertical centrada */
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9e9e9;
        }

        input[type="number"] {
            width: 50px;
            text-align: center;
        }

        h3#total_general {
            font-size: 24px;
            /* Tamaño de fuente más grande */
            text-align: center;
            /* Alineación a la derecha */
            margin-top: 10px;
            /* Espacio superior */
            margin-bottom: 0; /* Ajuste de margen */
        }

        button.realizar-pago-btn {
            background-color: #A0C3D2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button.realizar-pago-btn:hover {
            background-color: #A0C3D2;
        }

        /* Contenedor de acciones del checkout */
        .acciones-pago {
            text-align: right;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main>
        <section class="contenedor">
            <div class="contenedor-items">
                <div class="tarjetas-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lista_carrito)) : ?>
                                <tr>
                                    <td colspan="5"><b>Lista Vacía</b></td>
                                </tr>
                                <?php else :
                                $total_general = 0;
                                foreach ($lista_carrito as $producto) :
                                    $_id = $producto['id_producto'];
                                    $nombre = $producto['nombre_presentacion'];
                                    $precio = $producto['precio_venta'];
                                    $cantidad = $producto['cantidad'];
                                    $total = $cantidad * $precio;
                                    $total_general += $total;
                                ?>
                                    <tr>
                                        <td><?php echo $nombre; ?></td>
                                        <td><?php echo MONEDA . number_format($precio, 0, ',', '.'); ?></td>
                                        <td>
                                            <input type="number" min="1" max="99" step="1" value="<?php echo $cantidad; ?>" size="5" id="cantidad_<?php echo $_id; ?>" onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
                                        </td>
                                        <td id="total_<?php echo $_id; ?>" name="total[]"><?php echo MONEDA . number_format($total, 0, ',', '.'); ?></td>
                                        <td><button class="realizar-pago-btn" onclick="eliminarProducto(<?php echo $_id; ?>)">Eliminar</button></td>
                                    </tr>
                                <?php endforeach; ?>

                                <tr>
                                    <td colspan="3" style="text-align: right; padding-right: 15px; font-weight: bold; font-size: 1.1em;">
                                        Total General:
                                    </td>
                                    <td>
                                        <h3 id="total_general" name="total_general[]"><?php echo MONEDA . number_format($total_general, 0, ',', '.'); ?> </h3>
                                    </td>
                                    <td>
                                        <?php if (isset($_SESSION['user_cliente'])) { ?>
                                            <button onclick="location.href='pago.php'" class="realizar-pago-btn">Realizar Pago</button>
                                        <?php } else { ?>
                                            <button onclick="location.href='pago.php'" class="realizar-pago-btn">Realizar Pago</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php endif; ?> </tbody>
                    </table>

                    </div> </div>
        </section>
    </main>

    <script>
        function actualizaCantidad(cantidad, id) {
            let url = 'actualizar_carrito.php';
            let formData = new FormData();
            formData.append('action', 'agregar');
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Network response was not ok: ' + text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.ok) {
                        let divtotal = document.getElementById("total_" + id);
                        if (divtotal) {
                            divtotal.innerHTML = data.sub;
                        } else {
                            console.error('Element with ID total_' + id + ' not found.');
                        }

                        let total = 0;
                        let list = document.getElementsByName("total[]");

                        for (let i = 0; i < list.length; i++) {
                            total += parseFloat(list[i].innerHTML.replace(/[^\d]/g, ''));
                        }

                        total = new Intl.NumberFormat('es-CL', {
                            style: 'currency',
                            currency: 'CLP',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(total);

                        document.getElementById('total_general').innerHTML = total;
                    }
                }).catch(error => {
                    console.error('Error:', error.message);
                });
        }
    </script>
    <script>
        function eliminarProducto(id) {
            let url = 'eliminar_carrito.php';
            let formData = new FormData();
            formData.append('id', id);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Network response was not ok: ' + text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.ok) {
                        location.reload(); // Recarga la página para actualizar el carrito
                    }
                }).catch(error => {
                    console.error('Error:', error.message);
                });
        }
    </script>

</body>
<?php include 'footer.php'; ?>

</html>