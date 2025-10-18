<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset("utf8mb4");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // --- PASO 1: Recoger y validar datos ---
    $id_insumo = $_POST["id_insumo"] ?? null;
    $cantidad_actual = $_POST["cantidadActual"] ?? ''; 

    if (!$id_insumo) {
        $mensaje = "❌ Error: Debes seleccionar un insumo.";
    } else if ($cantidad_actual === '' || !is_numeric($cantidad_actual)) {
        $mensaje = "❌ Error: El campo 'Cantidad actual' es obligatorio y debe ser un número.";
    } else {
        // --- PASO 2: Construir la consulta dinámicamente ---
        $campos_a_actualizar = [];
        $parametros = [];
        $tipos_de_datos = "";

        $campos_a_actualizar[] = "cantidadActual = ?";
        $parametros[] = $cantidad_actual;
        $tipos_de_datos .= "d";

        if (!empty($_POST["precio_presentacion_compra"]) && is_numeric($_POST["precio_presentacion_compra"])) {
            $nuevo_precio_compra = $_POST["precio_presentacion_compra"];
            
            $campos_a_actualizar[] = "precio_presentacion_compra = ?";
            $parametros[] = $nuevo_precio_compra;
            $tipos_de_datos .= "d";

            $stmt_unidad = $conexion->prepare("SELECT unidad_med FROM insumos WHERE id_insumo = ?");
            $stmt_unidad->bind_param("i", $id_insumo);
            $stmt_unidad->execute();
            $resultado_unidad = $stmt_unidad->get_result()->fetch_assoc();
            $stmt_unidad->close();
            
            if ($resultado_unidad) {
                $unidad_med = $resultado_unidad['unidad_med'];
                $precio_calculado = $nuevo_precio_compra; 

                if (in_array($unidad_med, ['gramos', 'ml'])) {
                    $precio_calculado = $nuevo_precio_compra / 1000.0;
                }

                $campos_a_actualizar[] = "precio_por_gramos = ?"; // <-- CAMBIO AQUI
                $parametros[] = $precio_calculado;
                $tipos_de_datos .= "d";
            }
        }

        if (!empty($_POST["fecha_ingreso"])) {
            $campos_a_actualizar[] = "fecha_ingreso = ?";
            $parametros[] = $_POST["fecha_ingreso"];
            $tipos_de_datos .= "s";
        }

        if (!empty($_POST["fecha_vencimiento"])) {
            $campos_a_actualizar[] = "fecha_vencimiento = ?";
            $parametros[] = $_POST["fecha_vencimiento"];
            $tipos_de_datos .= "s";
        }

        // --- PASO 3: Ejecutar la consulta ---
        $sql = "UPDATE insumos SET " . implode(", ", $campos_a_actualizar) . " WHERE id_insumo = ?";
        
        $parametros[] = $id_insumo;
        $tipos_de_datos .= "i"; 

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($tipos_de_datos, ...$parametros);

        if ($stmt->execute()) {
            $mensaje = "✅ Insumo actualizado correctamente.";
        } else {
            $mensaje = "❌ Error al actualizar: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Actualizar Insumo</title>
  <link rel="stylesheet" href="../css/editarinsumo.css">
  <link rel="stylesheet" href="../css/style.css" />
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
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li><a href="inicioadmin.php">Panel de Gestión</a></li>
        <?php endif; ?>
        
        <li><a href="index.php">Inicio</a></li>
        <li><a href="../Productos/catalogo.php">Catálogo</a></li>
        <li><a href="../html/nosotros.html">Sobre Nosotros</a></li>
        <li><a href="../html/carrito.html">Carrito</a></li>

        <?php if (isset($_SESSION['usuario'])): ?>
            <li><a href="../html/perfil.html">Perfil (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a></li>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li><a href="procesar.php">Crear Receta</a></li>
                <li><a href="editarinsumo.php">Editar Insumos</a></li>
                <li><a href="historial.php">Historial de insumos</a></li>
            <?php endif; ?>
            
            <li><a href="logout.php">Cerrar sesión</a></li>
        <?php else: ?>
            <li><a href="login.php">Iniciar sesión</a></li>
            <li><a href="registro.php">Regístrate</a></li>
        <?php endif; ?>
    </ul>

    <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
</nav>
<script src="../js/index.js"></script>

  <form method="POST" action="editarinsumo.php">
    <h2>Actualizar insumo</h2>

    <label for="id_insumo">Selecciona insumo:</label>
    <select name="id_insumo" required>
        <option value="">-- Selecciona un insumo --</option>
        <?php
            $query_insumos = "SELECT id_insumo, nombre FROM insumos ORDER BY nombre ASC";
            $result_insumos = $conexion->query($query_insumos);
            while($insumo = $result_insumos->fetch_assoc()) {
                echo "<option value='{$insumo['id_insumo']}'>" . htmlspecialchars($insumo['nombre']) . "</option>";
            }
        ?>
    </select>

    <label for="cantidadActual">Cantidad actual (en gramos, ml o unidades):</label>
    <input type="number" step="0.01" name="cantidadActual" placeholder="Ej: 1500.50" required>

    <label for="precio_presentacion_compra">Precio de Compra (por Kilo/Litro/Paquete $):</label>
    <input type="number" step="0.01" name="precio_presentacion_compra" placeholder="-- Opcional: solo si el precio cambió --">

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

