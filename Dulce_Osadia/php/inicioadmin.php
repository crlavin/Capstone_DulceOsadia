<?php
require 'config/database.php';
require '../config/config.php';

// Solo permite acceso a administradores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
  header("Location: login.php");
  exit();
}
$db = new Database();

// Conectar a la base de datos
$con = $db->conectar();

// Preparar la consulta SQL
$comando = $con->prepare("SELECT id_producto, nombre_presentacion, precio_venta FROM presentaciones_venta");

// Ejecutar la consulta
$comando->execute();

// Obtener todos los resultados
$resultado = $comando->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="UTF-8">
  <title>Panel de acciones</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" type="text/css" href="../css/estilos.css">
  <link rel="stylesheet" type="text/css" href="../css/normalize.css">
  <link rel="stylesheet" type="text/css" href="../css/mobile.css">
  <link rel="stylesheet" type="text/html" href="../productos.php">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

  <?php include 'menu.php'; ?>
  <h1 style="text-align: center;">¿Qué deseas hacer hoy?</h1>

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
</body>
<?php include 'footer.php'; ?>
</html>