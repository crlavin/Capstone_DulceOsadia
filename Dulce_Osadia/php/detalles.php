<?php
require '../config/database.php';
require '../config/config.php';
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
        body {
            background-color: #fed794;
            background-image: url("../img/Patrones_rosados/Recurso_108.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main>
                <?php

                try {
                    $db = new Database();
                    $con = $db->conectar();

                    // Aceptar tanto 'id' como 'id_producto' para mayor compatibilidad
                    $id = $_GET['id_producto'] ?? $_GET['id'] ?? '';
                    $token = $_GET['token'] ?? '';

                    if (empty($id) || empty($token)) {
                        throw new Exception('Error: Parámetros no válidos');
                    }

                    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

                    if ($token !== $token_tmp) {
                        throw new Exception('Error: Token no válido');
                    }

                   
                    $sql = $con->prepare("SELECT 
                        pv.nombre_presentacion, 
                        pv.precio_venta, 
                        p.descripcion,  
                        pv.img, 
                        pv.estado,
                        p.SKU
                    FROM 
                        presentaciones_venta pv
                    INNER JOIN 
                        producto p ON pv.id_producto = p.id_producto
                    WHERE 
                        pv.id_producto = ? 
                    LIMIT 1");

                    $sql->execute([$id]);
                    $producto = $sql->fetch(PDO::FETCH_ASSOC);

                    if (!$producto) {
                        throw new Exception('Error: Producto no encontrado');
                    }

                    $nombre = $producto['nombre_presentacion'];
                    $img = $producto['img'];
                    $precio = $producto['precio_venta'];
                    $precio_formateado = number_format($precio, 0, ',', '.');
                    $descripcion = $producto['descripcion'];
                    $estado = $producto['estado'] ?? '';
                    $estadoClass = (strtolower(trim($estado)) === 'activo') ? 'in-stock' : 'out-stock';
                    $sku = $producto['SKU'] ?? '';

                    echo "<main class='product-detail'>";
                    echo "  <div class='product-gallery'>";
                    echo "    <img src='$img' alt='Imagen de $nombre'>";
                    echo "  </div>";
                    echo "  <div class='product-info'>";
                    echo "    <h1 class='product-title'>$nombre</h1>";
                    echo "    <p class='product-subtitle'>" . htmlspecialchars($descripcion) . "</p>";
                    echo "    <p class='product-price'>$ $precio_formateado</p>";
                    echo "    <p class='product-meta'><strong>SKU:</strong> " . htmlspecialchars($sku) . "</p>";
                    echo "    <p class='product-stock $estadoClass'>Estado: " . htmlspecialchars($estado) . "</p>";
                    echo "    <div class='qty-row'>";
                    echo "      <label for='cantidad'>Cantidad</label>";
                    echo "      <div class='qty-control'>";
                    echo "        <button type='button' class='qty-btn' data-change='-1'>-</button>";
                    echo "        <input type='number' id='cantidad' name='cantidad' min='1' max='99' value='1'>";
                    echo "        <button type='button' class='qty-btn' data-change='1'>+</button>";
                    echo "      </div>";
                    echo "    </div>";
                    echo "    <button class='btn-addcart' onclick=\"addProducto($id, document.getElementById('cantidad').value, '$token'); return false;\">Agregar al Carrito</button><br>";
                    echo "<br>";
                    echo "    <button class='btn-addcart' onclick=\"location.href='checkout.php?id=$id&token=$token'\">Comprar ahora</button>";
                    echo "    <div class='share'>";
                    echo "      <span>Compartir:</span>";
                    echo "      <a href='#'><i class='fab fa-facebook-f'></i></a>";
                    echo "      <a href='https://www.instagram.com/tu_usuario'><i class='fab fa-instagram'></i></a>";
                    echo "      <a href='#'><i class='fab fa-x'></i></a>";
                    echo "      <a href='#'><i class='fab fa-whatsapp'></i></a>";
                    echo "    </div>";
                    echo "</main>";

                    $con = null;
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                ?>
    </main>
    <script>
        // Controles de cantidad (estilo Productos)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('qty-btn')) {
                const input = document.getElementById('cantidad');
                const change = parseInt(e.target.getAttribute('data-change'), 10);
                const current = parseInt(input.value || '1', 10);
                const next = Math.max(1, Math.min(99, current + change));
                input.value = next;
            }
        });

        function addProducto(id, cantidad, token) {
            let url = 'carrito.php';
            let formData = new FormData();
            // Enviamos 'id' para máxima compatibilidad
            formData.append('id', id);
            formData.append('cantidad', cantidad);
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