<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$producto = $_POST["producto"] ?? null;
$cantidad = $_POST["cantidad_producto"] ?? null;

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

$id_receta = $mapa_recetas[$producto] ?? null;

if ($id_receta && $cantidad) {
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

  echo "<link rel='stylesheet' href='css/estilosopcion2.css'>";
  echo "<h2>Insumos necesarios para $cantidad unidades de " . str_replace("_", " ", $producto) . "</h2>";
  echo "<table border='1'><tr><th>Insumo</th><th>Cantidad total</th><th>Unidad</th><th>Costo total</th></tr>";

  while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>
      <td>{$fila['insumo']}</td>
      <td>{$fila['cantidad_total']}</td>
      <td>{$fila['unidad']}</td>
      <td>\${$fila['costo_total']}</td>
    </tr>";
  }

  echo "</table>";
} else {
  echo "<p style='color:red;'>No se encontraron insumos o faltan datos. Verifica la receta y cantidad.</p>";
}
?>
