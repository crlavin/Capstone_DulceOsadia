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

  <br />

  <!-- CARRUSEL -->
  <div class="w3-content w3-section" style="max-width:800px">
    <img class="mySlides" src="../img/carrusel.png" style="width:100%" alt="Nuez Choc" />
    <img class="mySlides" src="../img/Ustedes.png" style="width:100%" alt="Cuchuflies" />
    <img class="mySlides" src="../img/Catálogo.png" style="width:100%" alt="Bombón" />
  </div>

  <!-- TARJETAS -->
  <div class="tarjetas-container">

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/3.png" alt="Nuez Choc" />
      <h3>Nuez Choc</h3>
      <p>Ingredientes Principales: Nueces, Manjar y cobertura de chocolate.</p>
      <a href="../Productos/nuezchoc.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/4.png" alt="Cuchuflies" />
      <h3>Cuchuflies</h3>
      <p>Ingredientes Principales: Manjar, Vaina de trigo y cobertura de chocolate.</p>
      <a href="../Productos/cuchuflies.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/5.png" alt="Bombón" />
      <h3>BOMbon</h3>
      <p>Ingredientes Principales: Crema de maní, chocolate blanco, vaina de trigo y cobertura de chocolate.</p>
      <a href="../Productos/BOMbon.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/6.png" alt="Trufas Sabor Ron" />
      <h3>Trufas Sabor Ron</h3>
      <p>Ingredientes Principales: Manjar, esencia de ron, cobertura de chocolate y decoración de chocolate.</p>
      <a href="../Productos/trufas.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/7.png" alt="Alfajor Tradicional" />
      <h3>Alfajor Tradicional</h3>
      <p>Ingredientes Principales: Galletas alfajor origen argentino, Manjar y cobertura de chocolate.</p>
      <a href="../Productos/alfajor.php">Ver más</a>
    </div>

    
    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/alfablanco.png" alt="Alfajor Blanco" />
      <h3>Alfajor Tradicional Blanco</h3>
      <p>Ingredientes Principales:Galletas alfajor origen argentino, Manjar y cobertura de chocolate blanco. </p>
      <a href="../Productos/alfajortradiblanco.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/frambuesanegro.png" alt="Alfajor Frambuesa" />
      <h3>Alfajor Frambuesa</h3>
      <p>Ingredientes Principales: Galletas alfajor origen argentino, Mermelada frambuesa y cobertura de chocolate.</p>
      <a href="../Productos/alfajorfram.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/frambuesabl.png" alt="Alfajor Frambuesa Blanco" />
      <h3>Alfajor Frambuesa Chocolate Blanco</h3>
      <p>Ingredientes Principales: Galletas alfajor origen argentino, Mermelada frambuesa y cobertura de chocolate blanco. </p>
      <a href="../Productos/alfajorframblanco.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/8.png" alt="Bombón de Avellana" />
      <h3>Bombón de Avellana</h3>
      <p>Ingredientes Principales:</p>
      <a href="../Productos/bombonavellana.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/cocada.png" alt="Cocadas" />
      <h3>Cocadas</h3>
      <p>Ingredientes Principales: Manjar y coco</p>
      <a href="../Productos/cocadas.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/13.png" alt="Mix Bombones" />
      <h3>Mix Bombones</h3>
      <p>Bombones Principales:Trufas ron,cocadas,Nuez choc y BOMBOM mani . </p>
      <a href="../Productos/mixbombones.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/prestigio.png" alt="Prestigio Coco" />
      <h3>Prestigio Coco</h3>
      <p>Ingredientes Principales : Leche condensada,chocolate y coco.</p>
      <a href="../Productos/prestigiococo.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/14.png" alt="Mini Dubai" />
      <h3>Mini Barra de Dubai</h3>
      <p>Ingredientes Principales : Chocolate , Pistacho</p>
      <a href="../Productos/minidubai.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/15.png" alt="Dubai " />
      <h3>Barra Dubai </h3>
      <p>Ingredientes Principales:Chocolate , Pistacho </p>
      <a href="../Productos/dubai.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/9.png" alt="Nuez Choc sin azúcar" />
      <h3>Nuez Choc sin azúcar</h3>
      <p>Ingredientes Principales: Nueces, Manjar sin azúcar y cobertura de chocolate sin azúcar.</p>
      <a href="../Productos/nuezchocSA.php">Ver más</a>
    </div>

    <div class="tarjeta">
      <img src="../img/Recursosrecetasimg/1.png" alt="Trufas sin azúcar" />
      <h3>Trufas Sin Azúcar</h3>
      <p>Ingredientes Principales: Manjar sin azúcar, chocolate sin azúcar y cacao.</p>
      <a href="../Productos/trufasSA.php">Ver más</a>
    </div>

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
      if (myIndex > x.length) { myIndex = 1; }
      x[myIndex-1].style.display = "block";
      setTimeout(carousel, 3000);
    }
  </script>

</body>
<?php include_once __DIR__ . '/footer.php'; ?>
</html>
