<?php
require_once '../config/database.php';
require_once 'config/config.php';

// Crear una instancia de la clase Database
$db = new Database();

// Conectar a la base de datos
$con = $db->conectar();

// Preparar la consulta SQL (incluye imagen y precio desde presentaciones_venta)
$comando = $con->prepare("SELECT id_producto, nombre_presentacion, img, precio_venta FROM presentaciones_venta");

// Ejecutar la consulta
$comando->execute();

// Obtener todos los resultados
$resultado = $comando->fetchAll(PDO::FETCH_ASSOC);

// Traer 4 presentaciones para la sección "Más vendidos" con imagen y precio
$stmt_top = $con->prepare("SELECT pv.id_producto, pv.nombre_presentacion, pv.img, pv.precio_venta, p.descripcion FROM presentaciones_venta pv INNER JOIN producto p ON pv.id_producto = p.id_producto ORDER BY pv.id_presentacion ASC LIMIT 4");
$stmt_top->execute();
$top_productos = $stmt_top->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Dulce Osadia</title>
  <link rel="stylesheet" type="text/css" href="../css/estilos.css">
  <!-- stylesheet se refiere a la hoja de estilos, esto hace que agarre la info de esta misma -->
  <link rel="stylesheet" type="text/css" href="../css/normalize.css">
  <link rel="stylesheet" type="text/css" href="../css/mobile.css">
  <link rel="stylesheet" type="text/html" href="../productos.php">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
  <?php include 'menu.php'; ?>
  </ul>
  <style>
    body {
      background-color: #fed794;
      background-image: url("../img/Patrones_rosados/Recurso_108.png");
      background-repeat: repeat;
      background-size: cover;
      background-position: center;
    }

    /* Mejora visual para productos del index */
    .tarjetas-container {
      max-width: 1080px;
      margin: 20px auto;
      background: rgba(255, 255, 255, 0.92);
      padding: 24px 28px;
      border-radius: 14px;
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .tarjetas-container h2,
    .tarjetas-container h1 {
      color: #3a2a16;
      text-align: center;
      margin: 0 0 16px;
      text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .tarjeta h3 {
      font-size: 1.18rem;
      color: #4a331c;
      margin: 8px 0 6px;
    }

    .tarjeta p {
      font-size: 1.06rem;
      line-height: 1.7;
      color: #2e2415;
      margin: 4px 0 10px;
      letter-spacing: 0.2px;
    }

    .tarjeta img {
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
    }
  </style>

  <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>

  <script src="../js/index.js"></script>
  </nav>

  <br />

  <!-- CARRUSEL -->
  <div class="w3-content w3-section" style="max-width:800px">
    <img class="mySlides" src="../img/carrusel.png" style="width:100%" alt="Nuez Choc" />
    <img class="mySlides" src="../img/Ustedes.png" style="width:100%" alt="Cuchuflies" />
    <img class="mySlides" src="../img/Catálogo.png" style="width:100%" alt="Bombón" />
  </div>

   <h1 style="width:100%; text-align:center;">Productos más vendidos</h1>
  <div class="tarjetas-container" style="margin-top: 30px;">
    <?php if (!empty($top_productos)): ?>
      <?php foreach ($top_productos as $prod): ?>
        <?php
        $idTop = $prod['id_producto'];
        $nombreTop = $prod['nombre_presentacion'];
        $imgTop = isset($prod['img']) && $prod['img'] ? $prod['img'] : '../img/Catálogo.png';
        $precioTop = isset($prod['precio_venta']) ? (float)$prod['precio_venta'] : 0;
        $precioTopFmt = number_format($precioTop, 0, ',', '.');
        $descTop = $prod['descripcion'] ?? 'Producto destacado de nuestra carta.';
        $tokenTop = hash_hmac('sha1', $idTop, KEY_TOKEN);
        ?>
        <div class="tarjeta">
          <img src="<?= htmlspecialchars($imgTop) ?>" alt="<?= htmlspecialchars($nombreTop) ?>" />
          <h3><?= htmlspecialchars($nombreTop) ?></h3>
          <p><?= htmlspecialchars($descTop) ?></p>
          <p><strong>Precio:</strong> <?= defined('MONEDA') ? MONEDA : '$' ?><?= $precioTopFmt ?></p>
          <a href="detalles.php?id=<?= $idTop ?>&token=<?= $tokenTop ?>">Ver más</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="width:100%; text-align:center;">No hay productos disponibles para mostrar.</p>
    <?php endif; ?>
  </div>

  <!-- CARRUSEL JS -->
  <script>
    let myIndex = 0;
    carousel();

    function carousel() {
      let i;
      let x = document.getElementsByClassName("mySlides");
      for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
      }
      myIndex++;
      if (myIndex > x.length) {
        myIndex = 1;
      }
      x[myIndex - 1].style.display = "block";
      setTimeout(carousel, 3000);
    }
  </script>

</body>

<?php include 'footer.php'; ?>

</html>