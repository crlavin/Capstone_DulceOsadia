<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recetas</title>
    <link rel="stylesheet" href="../css/estilorecetas.css">
</head>
<body>
    <!-- NAVBAR -->
  
  <nav class="navbar">
    <link rel="stylesheet" href="../css/style.css" />
    <div class="logo">
      <a href="../index.php">
        <img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" />
      </a>
    </div>

    <div class="menu-toggle" id="menu-toggle">☰</div>

    <ul class="nav-link" id="nav-link">
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
  <li><a href="gestion_admin.php">Panel de Gestión</a></li>
  <?php endif; ?>
  <li><a href="index.php">Inicio</a></li>
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
     <h1>Fabrica de productos por receta</h1>
    <h2>¿Que desea fabricar hoy? , escoja un item</h2>
    <br>
    <div class="tarjetas-container">
  
  <div class="tarjeta">
    <img src="../img/Recursosrecetasimg/3.png" alt="Nuez Choc">
    <h3>Nuez Choc</h3>
    <p>Ingredientes Principales: Nueces,Manjar y cobertura de chocolate.</p>
    <a href="/Dulce_Osadia_html/Productos/nuezchoc.html">Ver más</a>
  </div>
  <div class="tarjeta">
  <img src="../img/Recursosrecetasimg/4.png" alt="Cuchuflies">
  <h3>Cuchuflies</h3>
  <p>Ingredientes Principales: Manjar, Vaina de trigo y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/cuchuflies.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/5.png" alt="BOMbon">
  <h3>BOMbon</h3>
  <p>Ingredientes Principales: Crema de maní, chocolate blanco, vaina de trigo y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/BOMbon.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/6.png" alt="Trufas Sabor ron">
  <h3>Trufas Sabor ron</h3>
  <p>Ingredientes Principales: Manjar, Esencia de ron, cobertura de chocolate y decoración de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/trufas.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/7.png" alt="Alfajor Tradicional">
  <h3>Alfajor Tradicional</h3>
  <p>Ingredientes Principales: Galletas alfajor origen argentino, Manjar y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/alfajor.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/alfablanco.png" alt="Alfajor Tradiblanco">
  <h3>Alfajor Tradicional blanco</h3>
  <p>Ingredientes Principales: Galletas, Manjar y chocolate blanco.</p>
  <a href="/Dulce_Osadia_html/Productos/alfajortradiblanco.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/frambuesanegro.png" alt="Alfajor Frambuesa">
  <h3>Alfajor Frambuesa</h3>
  <p>Ingredientes Principales: Galletas, Manjar y mermelada de frambuesa.</p>
  <a href="/Dulce_Osadia_html/Productos/alfajorfram.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/frambuesabl.png" alt="Alfajor Frambuesa Blanco">
  <h3>Alfajor Frambuesa Blanco</h3>
  <p>Ingredientes Principales: Galletas, Manjar, mermelada de frambuesa y chocolate blanco.</p>
  <a href="/Dulce_Osadia_html/Productos/alfajorframblanco.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/8.png" alt="Bombón de avellana">
  <h3>Bombón de avellana</h3>
  <p>Ingredientes Principales: Avellanas, crema y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/bombonavellana.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/cocada.png" alt="Cocadas">
  <h3>Cocadas</h3>
  <p>Ingredientes Principales: Coco rallado, leche condensada y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/cocadas.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/13.png" alt="Mix Bombones">
  <h3>Mix Bombones</h3>
  <p>Ingredientes Principales: Variedad de bombones con diferentes rellenos y coberturas.</p>
  <a href="/Dulce_Osadia_html/Productos/mixbombones.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/prestigio.png" alt="Prestigio Coco">
  <h3>Prestigio Coco</h3>
  <p>Ingredientes Principales: Coco rallado, crema y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/prestigiococo.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/15.png" alt="Dubai">
  <h3>Dubai</h3>
  <p>Ingredientes Principales: Pistacho, crema y cobertura de chocolate.</p>
  <a href="/Dulce_Osadia_html/Productos/dubai.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/14.png" alt="Mini Dubai">
  <h3>Mini Dubai</h3>
  <p>Ingredientes Principales: Pistacho, crema y cobertura de chocolate en formato mini.</p>
  <a href="/Dulce_Osadia_html/Productos/minidubai.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/9.png" alt="Nuez Choc sin azucar">
  <h3>Nuez Choc sin azucar</h3>
  <p>Ingredientes Principales: Nueces, Manjar sin azúcar y cobertura de chocolate sin azúcar.</p>
  <a href="/Dulce_Osadia_html/Productos/nuezchocsinazucar.html">Ver más</a>
</div>

<div class="tarjeta">
  <img src="../img/Recursosrecetasimg/1.png" alt="Trufas Sin Azucar">
  <h3>Trufas Sin Azucar</h3>
  <p>Ingredientes Principales: Manjar sin azúcar, chocolate sin azúcar y cacao.</p>
  <a href="/Dulce_Osadia_html/Productos/trufassinazucar.html">Ver más</a>
</div>

  
</body>
</html>