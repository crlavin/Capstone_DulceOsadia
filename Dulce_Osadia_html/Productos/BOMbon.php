<?php
session_start();


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dulce Osadía</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<style>
  body {
    background-color: #fed794;
    background-image: url("../img/Patrones_rosados/Recurso_108.png");
    background-repeat: repeat;
    background-size: cover;
    background-position: center;
  }
</style>
  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="logo">
      <a href="../php/index.php">
        <img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" />
      </a>
    </div>

    <div class="menu-toggle" id="menu-toggle">☰</div>

    <ul class="nav-link" id="nav-link">
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
  <li><a href="gestion_admin.php">Panel de Gestión</a></li>
  <?php endif; ?>
  <li><a href="../php/index.php">Inicio</a></li>
  <li><a href="../Productos/catalogo.php">Catálogo</a></li>
  <li><a href="../html/nosotros.html">Sobre Nosotros</a></li>
  <li><a href="../html/carrito.html">Carrito</a></li>

  <?php if (isset($_SESSION['usuario'])): ?>
    <!-- Usuario autenticado -->
    <li><a href="../html/perfil.html">Perfil (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a></li>
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
      <li><a href="procesar.php">Crear Receta</a></li>
      <li><a href="editarinsumo.php">Editar Insumos</a></li>
    <?php endif; ?>
    <li><a href="logout.php">Cerrar sesión</a></li>
  <?php else: ?>
    <!-- Usuario no autenticado -->
    <li><a href="login.php">Iniciar sesión</a></li>
    <li><a href="registro.php">Regístrate</a></li>
  <?php endif; ?>
</ul>


    <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>

    <script src="../js/index.js"></script>
  </nav>
  <?php
    $product = [
      'title' => 'Bombón Clásico',
      'subtitle' => 'Caja 220 grs aprox.',
      'price' => 24900,
      'sku' => 'BMB-CLS-001',
      'category' => 'Bombones',
      'image' => '../img/Recursosrecetasimg/5.png',
      'available' => true,
    ];
    include_once '../php/product_template.php';
  ?>