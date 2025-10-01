<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Solo admins pueden acceder
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
  header('Location: index.php');
  exit();
}

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset('utf8mb4');

$resultado = null;
$mensaje_error = null;

$mapa_recetas = [
  "bombon_crema_mani" => 1,
  "nuez_rellena" => 2,
  "bombon_avellana" => 3,
  "cuchuflie" => 4,
  "alfajor_tradicional" => 5,
  "alfajor_tradicional_blanco" => 6,
  "alfajor_frambuesa_blanco" => 7,
  "alfajor_frambuesa_negro" => 8,
  "chocolates_sin_azucar" => 9,
  "nuez_sin_azucar" => 10,
  "cocadas" => 11,
  "trufas_ron" => 12,
  "prestigio_coco" => 13,
  "mix_bombones" => 14,
  "barra_dubai" => 15,
  "mini_barra_dubai" => 16
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $producto = $_POST["producto"] ?? null;
  $cantidad = $_POST["cantidad_producto"] ?? null;
  $id_receta = $mapa_recetas[$producto] ?? null;

  if ($id_receta && is_numeric($cantidad) && $cantidad > 0) {
    $sql = "
      SELECT 
        i.nombre AS insumo,
        dr.cantidad_usada * ? AS cantidad_total,
        dr.unidad,
        i.precioUnitario,
        ROUND((dr.cantidad_usada * ? * i.precioUnitario / 1000), 2) AS costo_total
      FROM detalleReceta dr
      JOIN insumos i ON dr.id_insumo = i.id_insumo
      WHERE dr.id_receta = ?
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ddi", $cantidad, $cantidad, $id_receta);
    $stmt->execute();
    $resultado = $stmt->get_result();
  } else {
    $mensaje_error = "No se encontraron insumos o faltan datos. Verifica la receta y cantidad.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulador de Recetas</title>
  <link rel="stylesheet" href="../css/estilosopcion2.css">
</head>
<body>
  <!-- NAVBAR -->
  
  <nav class="navbar">
    <link rel="stylesheet" href="../css/style.css" />
    <div class="logo">
      <a href="index.php">
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


  <form method="POST" action="">
    <h2>Planificar receta</h2>

    <!-- Producto final -->
    <label for="producto">Producto final:</label>
    <select id="producto" name="producto" required>
      <option value="">-- Selecciona producto --</option>
      <?php foreach ($mapa_recetas as $clave => $id): 
        $nombre = ucwords(str_replace("_", " ", $clave));
        $selected = (isset($_POST['producto']) && $_POST['producto'] === $clave) ? 'selected' : '';
        echo "<option value='$clave' $selected>$nombre</option>";
      endforeach; ?>
    </select>

    <!-- Cantidad deseada -->
    <label for="cantidad_producto">¿Cuántas unidades quieres producir?</label>
    <input type="number" id="cantidad_producto" name="cantidad_producto" min="1" required value="<?= htmlspecialchars($_POST['cantidad_producto'] ?? '') ?>">

    <button type="submit">Simular disponibilidad</button>
  </form>

  <?php if ($mensaje_error): ?>
    <p style="color:red;"><?= $mensaje_error ?></p>
  <?php elseif ($resultado && $resultado->num_rows > 0): ?>
    <h2>Insumos necesarios para <?= htmlspecialchars($cantidad) ?> unidades de <?= str_replace("_", " ", $producto) ?></h2>
    <table border="1">
      <tr>
        <th>Insumo</th>
        <th>Cantidad total</th>
        <th>Unidad</th>
        <th>Costo total</th>
      </tr>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($fila['insumo']) ?></td>
          <td><?= htmlspecialchars($fila['cantidad_total']) ?></td>
          <td><?= htmlspecialchars($fila['unidad']) ?></td>
          <td>$<?= htmlspecialchars($fila['costo_total']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</body>
</html>
