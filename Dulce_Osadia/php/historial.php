<?php
require '../config/database.php';
require '../config/config.php';

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

// Consulta para obtener los insumos ordenados por la última actualización (los más recientes primero)
// LIMIT 50 para no sobrecargar la página si tienes muchos insumos. Puedes ajustar este número.
$sql = "SELECT nombre, cantidadActual, ultima_actualizacion, precio_por_gramos, unidad_med, fecha_vencimiento
        FROM insumos 
        WHERE ultima_actualizacion IS NOT NULL
        ORDER BY ultima_actualizacion DESC 
        LIMIT 50";

$stmt_historial = $con->query($sql);
$resultado_insumos = $stmt_historial ? $stmt_historial->fetchAll(PDO::FETCH_ASSOC) : [];

?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Historial de Insumos</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/historial.css">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" type="text/html" href="../productos.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container">
        <h1>Historial de Actualizaciones de Insumos</h1>
        <p>Mostrando los 50 cambios más recientes.</p>

        <table class="historial-table">
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Última Actualización</th>
                    <th>Stock Actual (g)</th>
                    <th>Precio por Kilo/ </th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resultado_insumos)): ?>
                    <?php foreach ($resultado_insumos as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td class="fecha">
                                <?php
                                // Formateamos la fecha para que sea más legible
                                $fecha = new DateTime($fila['ultima_actualizacion']);
                                echo $fecha->format('d/m/Y H:i');
                                ?>
                            </td>
                            <td><?php echo number_format($fila['cantidadActual'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                $ppg = isset($fila['precio_por_gramos']) ? (float)$fila['precio_por_gramos'] : 0.0;
                                if ($ppg > 0) {
                                    // Precio por kilo/litro (unidad_med gramos/ml) o por unidad si otra cosa
                                    $precio_kilo = $ppg * 1000.0;
                                    echo '$ ' . number_format($precio_kilo, 0, ',', '.');
                                } else {
                                    echo '—';
                                }
                                ?>
                            </td>
                            <td class="fecha">
                                <?php
                                $fv = $fila['fecha_vencimiento'] ?? null;
                                if ($fv && $fv !== '0000-00-00') {
                                    $v = new DateTime($fv);
                                    echo $v->format('d/m/Y');
                                } else {
                                    echo 'No registrada';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay registros de actualizaciones para mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>