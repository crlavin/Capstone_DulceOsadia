<?php
require_once 'config/database.php';
require_once '../config/config.php';
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
</head>

<body>
    <?php include 'menu.php'; ?>
    <style>
        body {
            background-color: #fed794;
            background-image: url("../img/Patrones_rosados/Recurso_108.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
    <!-- Main Content -->
    <main>
        <div class="tarjetas-container">
                <?php

                try {
                    $db = new Database();
                    $con = $db->conectar();
                    $sql = $con->prepare("SELECT id_producto, nombre_presentacion, img, precio_venta FROM presentaciones_venta");
                    $sql->execute();
                    $resultados = $sql->fetchAll(PDO::FETCH_ASSOC);

                    if ($resultados) {
                        foreach ($resultados as $producto) {
                            $id = $producto['id_producto'];
                            $nombre = $producto['nombre_presentacion'];
                            $img = $producto['img'];
                            $precio = $producto['precio_venta'];
                            $precio_formateado = number_format($precio, 0, ',', '.');
                            $token = hash_hmac('sha1', $id, KEY_TOKEN);

                            echo '<div class="tarjeta">';
                            echo "<img src='$img' alt='Imagen de $nombre'>";
                            echo "<h3>$nombre</h3>";
                            echo "<p>Precio: $$precio_formateado</p>";
                            echo "<a href='#' onclick=\"addProducto($id, '$token'); return false;\">Agregar al Carrito</a>";
                            echo "<br>";
                            echo "<a href='detalles.php?id=$id&token=$token'>Detalles</a>";
                            echo '</div>';
                        }
                    } else {
                        echo "No se encontraron productos.";
                    }

                    $con = null;
                } catch (PDOException $e) {
                    error_log("Database Error: " . $e->getMessage());
                    echo "Ocurrió un error al recuperar los productos.";
                }
                ?>

        </div>
    </main>
    <script>
        function addProducto(id, token) {
            let url = 'carrito.php';
            let formData = new FormData();
            formData.append('id', id);
            formData.append('token', token);

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
                        let elemento = document.getElementById("num_cart");
                        elemento.innerHTML = data.numero;
                    }
                }).catch(error => {
                    console.error('Error:', error.message);
                });
        }
    </script>

</body>
<?php include 'footer.php'; ?>

</html>