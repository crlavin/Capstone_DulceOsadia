<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // --- PASO 1: Recoger datos y validar el ID ---
    $id = $_POST["id_insumo"] ?? null;
    if (!$id) {
        $mensaje = "❌ Error: Debes seleccionar un insumo.";
    } else {
        // --- PASO 2: Construir la consulta dinámicamente ---
        $campos_a_actualizar = [];
        $parametros = [];
        $tipos_de_datos = "";

        // Campo obligatorio: Cantidad actual
        $campos_a_actualizar[] = "cantidadActual = ?";
        $parametros[] = $_POST["cantidadActual"];
        $tipos_de_datos .= "d"; // 'd' para decimal/double

        // Campo opcional: Precio unitario
        if (!empty($_POST["precioUnitario"])) {
            $campos_a_actualizar[] = "precioUnitario = ?";
            $parametros[] = $_POST["precioUnitario"];
            $tipos_de_datos .= "d";
        }

        // Campo opcional: Fecha de ingreso
        if (!empty($_POST["fecha_ingreso"])) {
            $campos_a_actualizar[] = "fecha_ingreso = ?";
            $parametros[] = $_POST["fecha_ingreso"];
            $tipos_de_datos .= "s"; // 's' para string (date)
        }

        // Campo opcional: Fecha de vencimiento
        if (!empty($_POST["fecha_vencimiento"])) {
            $campos_a_actualizar[] = "fecha_vencimiento = ?";
            $parametros[] = $_POST["fecha_vencimiento"];
            $tipos_de_datos .= "s";
        }

        // --- PASO 3: Ejecutar la consulta ---
        if (!empty($campos_a_actualizar)) {
            // Unir las partes de la consulta
            $sql = "UPDATE insumos SET " . implode(", ", $campos_a_actualizar) . " WHERE id_insumo = ?";
            
            // Añadir el ID del insumo al final de los parámetros y su tipo
            $parametros[] = $id;
            $tipos_de_datos .= "i"; // 'i' para integer

            $stmt = $conexion->prepare($sql);
            // Vincular los parámetros dinámicamente
            $stmt->bind_param($tipos_de_datos, ...$parametros);

            if ($stmt->execute()) {
                $mensaje = "✅ Insumo actualizado correctamente.";
            } else {
                $mensaje = "❌ Error al actualizar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje = "❌ No se proporcionaron datos para actualizar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Actualizar Insumo</title>
  <link rel="stylesheet" href="../css/editarinsumo.css">
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

    <label for="precioUnitario">Precio unitario por kilo($):</label>
    <input type="number" step="100" name="precioUnitario" placeholder="-- Solo si el precio ha cambiado --">

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
