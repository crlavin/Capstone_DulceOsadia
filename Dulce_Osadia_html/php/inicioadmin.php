<?php
session_start();

// Solo permite acceso a administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de acciones</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="logo">
      <a href="index.php">
        <img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" />
      </a>
    </div>

    <div class="menu-toggle" id="menu-toggle">☰</div>

    <ul class="nav-link" id="nav-link">
      <li><a href="gestion_admin.php">Panel de Gestión</a></li>
      <li><a href="index.php">Inicio</a></li>
      <li><a href="../Productos/catalogo.php">Catálogo</a></li>
      <li><a href="../html/nosotros.html">Sobre Nosotros</a></li>
      <li><a href="../html/carrito.html">Carrito</a></li>
      <li><a href="../html/perfil.html">Perfil (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a></li>
      <li><a href="logout.php">Cerrar sesión</a></li>
    <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
    
    </ul>
  </nav>


  <h1>¿Qué deseas hacer hoy?</h1>

  <!-- TARJETAS -->
  <div class="tarjetas-container">
    <div class="tarjeta">
      <img src="../img/inventario.png" alt="Editar Insumos">
      <h3>Editar Insumos</h3>
      <p>Gestiona el inventario, actualiza precios, cantidades y fechas de vencimiento.</p>
      <a href="editarinsumo.php">Ir al inventario</a>
    </div>

    <div class="tarjeta">
      <img src="../img/fabricacion.png" alt="Procesar Recetas">
      <h3>Procesar Recetas</h3>
      <p>Simula recetas, calcula costos por unidad y genera fichas técnicas.</p>
      <a href="procesar.php">Ir al simulador</a>
    </div>
  </div>

  <script src="../js/index.js"></script>
</body>
</html>
