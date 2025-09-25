<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

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
$stmt->execute();

echo "<p> Insumo actualizado correctamente.</p>";
?>
