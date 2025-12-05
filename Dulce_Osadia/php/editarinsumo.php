<?php
require_once 'config/database.php';
require_once 'config/config.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$db = new Database();
$con = $db->conectar();

$mensaje = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_insumo = $_POST["id_insumo"] ?? null;
    $cantidad_actual = $_POST["cantidadActual"] ?? '';
    $precio_presentacion_compra = $_POST["precio_presentacion_compra"] ?? '';
    $fecha_ingreso = $_POST["fecha_ingreso"] ?? '';
    $fecha_vencimiento = $_POST["fecha_vencimiento"] ?? '';
    $id_proveedor = $_POST["id_proveedor"] ?? '';
    $id_familiado = $_POST["id_familiado"] ?? '';

    if (!$id_insumo) {
        $mensaje = "❌ Error: Debes seleccionar un insumo.";
    } else if ($cantidad_actual === '' || !is_numeric($cantidad_actual)) {
        $mensaje = "❌ Error: 'Cantidad actual' es obligatoria y numérica.";
    } else {
        // Obtener unidad del insumo para calcular precio_por_gramos
        $unidad_stmt = $con->prepare("SELECT unidad_med FROM insumos WHERE id_insumo = ?");
        $unidad_stmt->execute([$id_insumo]);
        $unidad = $unidad_stmt->fetchColumn();

        $campos = [];
        $params = [];

        $campos[] = "cantidadActual = ?";
        $params[] = $cantidad_actual;

        if ($precio_presentacion_compra !== '' && is_numeric($precio_presentacion_compra)) {
            $campos[] = "precio_presentacion_compra = ?";
            $params[] = $precio_presentacion_compra;

            $ppg = ($unidad === 'gramos' || $unidad === 'ml') ? ($precio_presentacion_compra / 1000.0) : $precio_presentacion_compra;
            $campos[] = "precio_por_gramos = ?";
            $params[] = $ppg;
        }

        if ($fecha_ingreso !== '') {
            $campos[] = "fecha_ingreso = ?";
            $params[] = $fecha_ingreso;
        } else {
            $campos[] = "fecha_ingreso = NULL";
        }

        if ($fecha_vencimiento !== '') {
            $campos[] = "fecha_vencimiento = ?";
            $params[] = $fecha_vencimiento;
        } else {
            $campos[] = "fecha_vencimiento = NULL";
        }

        if ($id_proveedor !== '' && is_numeric($id_proveedor)) {
            $campos[] = "id_proveedor = ?";
            $params[] = $id_proveedor;
        }

        if ($id_familiado !== '' && is_numeric($id_familiado)) {
            $campos[] = "id_familiado = ?";
            $params[] = $id_familiado;
        }

        $campos[] = "ultima_actualizacion = NOW()";

        $sql = "UPDATE insumos SET " . implode(", ", $campos) . " WHERE id_insumo = ?";
        $params[] = $id_insumo;

        try {
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            $mensaje = "✅ Insumo actualizado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "❌ Error al actualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Actualizar Insumo</title>
    <link rel="stylesheet" href="../css/editarinsumo.css">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" type="text/html" href="../productos.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include 'menu.php'; ?>

    <form method="POST" action="editarinsumo.php">
        <h2>Actualizar insumo</h2>

        <label for="id_insumo">Selecciona insumo:</label>
        <select name="id_insumo" require_onced>
            <option value="">-- Selecciona un insumo --</option>
            <?php
            $query_insumos = "SELECT id_insumo, nombre FROM insumos ORDER BY nombre ASC";
            $result_insumos = $con->query($query_insumos);
            while ($insumo = $result_insumos->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='" . htmlspecialchars($insumo['id_insumo']) . "'>" . htmlspecialchars($insumo['nombre']) . "</option>";
            }
            ?>
        </select>

        <label for="cantidadActual">Cantidad actual (en gramos, ml o unidades):</label>
        <input type="number" step="0.01" name="cantidadActual" placeholder="Ej: 1500.50" require_onced>

        <label for="precio_presentacion_compra">Precio de Compra (por Kilo/Litro/Paquete $):</label>
        <input type="number" step="0.01" name="precio_presentacion_compra" placeholder="-- Opcional: solo si el precio cambió --">

        <label for="fecha_ingreso">Fecha ingreso:</label>
        <input type="date" name="fecha_ingreso">

        <label for="fecha_vencimiento">Fecha vencimiento:</label>
        <input type="date" name="fecha_vencimiento">

        <button type="submit">Actualizar</button>
    </form>

    <?php include 'footer.php'; ?>
</body>

</html>