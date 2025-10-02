<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
  header('Location: ../index.php'); // o login.php si prefieres
  exit();
}
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = $_POST["id_insumo"];
  $cantidad = $_POST["cantidadActual"];
  $precio = $_POST["precioUnitario"];
  $fecha_ingreso = $_POST["fecha_ingreso"];
  $fecha_vencimiento = $_POST["fecha_vencimiento"];

  $sql = "
    UPDATE insumos
    SET cantidadActual = ?, precioUnitario = ?, fecha_ingreso = ?, fecha_vencimiento = ?
    WHERE id_insumo = ?
  ";

  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("ddssi", $cantidad, $precio, $fecha_ingreso, $fecha_vencimiento, $id);

  if ($stmt->execute()) {
    $mensaje = "✅ Insumo actualizado correctamente.";
  } else {
    $mensaje = "❌ Error al actualizar: " . $stmt->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Actualizar Insumo</title>
  <link rel="stylesheet" href="css/editarinsumo.css">
</head>

<body>
  <form method="POST" action="editarinsumo.php">
    <h2>Actualizar insumo</h2>

    <label for="id_insumo">Selecciona insumo:</label>
    <select name="id_insumo" required>
      <option value="">-- Selecciona insumo --</option>
      <option value="1">Chocolate blanco Caravella</option>
      <option value="2">Chocolate negro Costa</option>
      <option value="3">Chocolate amargo sin azúcar Neucober</option>
      <option value="4">Maní sin sal</option>
      <option value="5">Crema de avellanas Halta</option>
      <option value="6">Nueces</option>
      <option value="7">Manjar Colun</option>
      <option value="8">Manjar sin azúcar Langer</option>
      <option value="9">Vaina de trigo Conebric</option>
      <option value="10">Galletas Fruna</option>
      <option value="11">Galletas Alfajor del Valle</option>
      <option value="12">Mermelada de frambuesa Langer</option>
      <option value="13">Coco rallado</option>
      <option value="14">Ron (esencia)</option>
      <option value="15">Leche condensada</option>
      <option value="16">Pistacho</option>
      <option value="17">Naranja(esencia)</option>
    </select>

    <label for="cantidadActual">Cantidad actual (g):</label>
    <input type="number" step="0.01" name="cantidadActual" required>

    <label for="precioUnitario">Precio unitario ($):</label>
    <input type="number" step="0.01" name="precioUnitario" required>

    <label for="fecha_ingreso">Fecha ingreso:</label>
    <input type="date" name="fecha_ingreso">

    <label for="fecha_vencimiento">Fecha vencimiento:</label>
    <input type="date" name="fecha_vencimiento">

    <button type="submit">Actualizar</button>
  </form>

  <?php if ($mensaje): ?>
    <div class="resultado"><?php echo $mensaje; ?></div>
  <?php endif; ?>
</body>
</html>
