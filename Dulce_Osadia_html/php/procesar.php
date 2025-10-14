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

// --- MENSAJES PARA EL USUARIO ---
$mensaje_info = $_SESSION['mensaje_info'] ?? null;
unset($_SESSION['mensaje_info']);

$resultado_plan = null;
$mensaje_error = null;
$stock_suficiente = true; 

// --- MAPA DE RECETAS ---
$mapa_recetas = [
    "bombon_crema_mani" => 1, "nuez_rellena" => 2, "bombon_avellana" => 3,
    "cuchuflie" => 4, "alfajor_tradicional" => 5, "alfajor_tradicional_blanco" => 6,
    "alfajor_frambuesa_blanco" => 7, "alfajor_frambuesa_negro" => 8, "chocolates_sin_azucar" => 9,
    "nuez_sin_azucar" => 10, "cocadas" => 11, "trufas_ron" => 12, "prestigio_coco" => 13,
    "mix_bombones" => 14, "barra_dubai" => 15, "mini_barra_dubai" => 16, "Trufa_Naranja" => 17
];

// Variable para controlar qué audio sonar
$audio_a_reproducir = null; 

// --- LÓGICA DE ACCIONES ---

// ACCIÓN 1: PLANIFICAR Y VERIFICAR STOCK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'planificar') {
    unset($_SESSION['ultima_reserva']);
    $producto = $_POST["producto"] ?? null;
    $cantidad = $_POST["cantidad_producto"] ?? null;
    $id_receta = $mapa_recetas[$producto] ?? null;

    if ($producto === 'bombon_crema_mani') {
        $audio_a_reproducir = 'Bombon';
    }

    if ($id_receta && is_numeric($cantidad) && $cantidad > 0) {
        $sql = "
            SELECT 
                i.id_insumo, i.nombre AS insumo, dr.cantidad_usada * ? AS cantidad_necesaria,
                i.cantidadActual AS cantidad_disponible, dr.unidad,
                ROUND((dr.cantidad_usada * ? * i.precioUnitario / 1000), 2) AS costo_total
            FROM detalleReceta dr
            JOIN insumos i ON dr.id_insumo = i.id_insumo
            WHERE dr.id_receta = ?
        ";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ddi", $cantidad, $cantidad, $id_receta);
        $stmt->execute();
        $resultado_plan = $stmt->get_result();

        $_SESSION['planificacion_actual'] = ['producto' => $producto, 'cantidad' => $cantidad, 'insumos' => []];

        while ($fila = $resultado_plan->fetch_assoc()) {
            $_SESSION['planificacion_actual']['insumos'][] = $fila;
            if ($fila['cantidad_disponible'] < $fila['cantidad_necesaria']) {
                $stock_suficiente = false;
            }
        }
        $resultado_plan->data_seek(0); 

    } else {
        $mensaje_error = "Por favor, selecciona un producto y una cantidad válida.";
    }
}

// ACCIÓN 2: CONFIRMAR Y RESERVAR INSUMOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'confirmar') {
    if (isset($_SESSION['planificacion_actual'])) {
        $plan = $_SESSION['planificacion_actual'];
        $conexion->begin_transaction();

        try {
            $stmt_update = $conexion->prepare("UPDATE insumos SET cantidadActual = cantidadActual - ? WHERE id_insumo = ?");

            foreach ($plan['insumos'] as $insumo) {
                $stmt_update->bind_param("di", $insumo['cantidad_necesaria'], $insumo['id_insumo']);
                $stmt_update->execute();
            }

            $conexion->commit();
            $_SESSION['mensaje_info'] = "¡Reserva exitosa! El stock ha sido actualizado.";
            $_SESSION['ultima_reserva'] = $plan['insumos'];
            unset($_SESSION['planificacion_actual']);

        } catch (mysqli_sql_exception $exception) {
            $conexion->rollback();
            $_SESSION['mensaje_info'] = "Error al reservar. No se modificó el stock. " . $exception->getMessage();
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// ACCIÓN 3: CANCELAR LA ÚLTIMA RESERVA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cancelar') {
    if (isset($_SESSION['ultima_reserva'])) {
        $ultima_reserva = $_SESSION['ultima_reserva'];
        $conexion->begin_transaction();

        try {
            $stmt_revert = $conexion->prepare("UPDATE insumos SET cantidadActual = cantidadActual + ? WHERE id_insumo = ?");
            foreach ($ultima_reserva as $insumo) {
                $stmt_revert->bind_param("di", $insumo['cantidad_necesaria'], $insumo['id_insumo']);
                $stmt_revert->execute();
            }
            $conexion->commit();
            $_SESSION['mensaje_info'] = "Reserva cancelada. Se ha devuelto el stock.";
            unset($_SESSION['ultima_reserva']);

        } catch (mysqli_sql_exception $exception) {
            $conexion->rollback();
            $_SESSION['mensaje_info'] = "Error al cancelar la reserva. " . $exception->getMessage();
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale-1.0">
  <title>Planificador de Recetas</title>
  <link rel="stylesheet" href="../css/estilosopcion2.css">
</head>
<body>
    <nav class="navbar">
      <link rel="stylesheet" href="../css/style.css" />
      <div class="logo"><a href="index.php"><img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" /></a></div>
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
          <li><a href="../html/perfil.html">Perfil (<?php echo htmlspecialchars($_SESSION['nombre']); ?>)</a></li>
          <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li><a href="procesar.php">Crear Receta</a></li>
            <li><a href="editarinsumo.php">Editar Insumos</a></li>
          <?php endif; ?>
          <li><a href="logout.php">Cerrar sesión</a></li>
        <?php else: ?>
          <li><a href="login.php">Iniciar sesión</a></li>
          <li><a href="registro.php">Regístrate</a></li>
        <?php endif; ?>
      </ul>
      <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
      <script src="../js/index.js"></script>
    </nav>
    
    <div class="form-container">
        <form method="POST" action="">
            <h2>Planificar receta</h2>

            <label for="producto">Producto final:</label>
            <select id="producto" name="producto" required>
                <option value="">-- Selecciona producto --</option>
                <?php foreach ($mapa_recetas as $clave => $id): 
                    $nombre = ucwords(str_replace("_", " ", $clave));
                    $selected = (isset($_POST['producto']) && $_POST['producto'] === $clave) ? 'selected' : '';
                    echo "<option value='$clave' $selected>$nombre</option>";
                endforeach; ?>
            </select>

            <label for="cantidad_producto">¿Cuántas unidades quieres producir?</label>
            <input type="number" id="cantidad_producto" name="cantidad_producto" min="1" required value="<?= htmlspecialchars($_POST['cantidad_producto'] ?? '') ?>">

            <button type="submit" name="accion" value="planificar">Planificar</button>
            <?php if ($audio_a_reproducir): ?>
            <div class="audio-player-container">
                <h4>Narración de la Receta:</h4>
                <audio controls autoplay>
                    <source src="../audios/<?php echo htmlspecialchars($audio_a_reproducir); ?>.mp3" type="audio/mpeg">
                    Tu navegador no soporta el elemento de audio.
                </audio>
            </div>
        <?php endif; ?>
        </div>
        </form>

        <?php if ($mensaje_info): ?><p class="mensaje-info"><?= $mensaje_info ?></p><?php endif; ?>
        <?php if ($mensaje_error): ?><p class="mensaje-error"><?= $mensaje_error ?></p><?php endif; ?>

        <?php if ($resultado_plan && $resultado_plan->num_rows > 0): ?>
            <h2>Insumos para <?= htmlspecialchars($_SESSION['planificacion_actual']['cantidad']) ?> unidades de <?= ucwords(str_replace("_", " ", $_SESSION['planificacion_actual']['producto'])) ?></h2>
            

            <?php if ($stock_suficiente): ?>
                <form method="POST" action="">
                <table border="1" style="margin: 20px auto; text-align: center;">
                <tr>
                    <th>Insumo</th><th>Cantidad Necesaria</th><th>Cantidad en Bodega</th><th>Stock Restante</th><th>Unidad</th>
                </tr>
                <?php while ($fila = $resultado_plan->fetch_assoc()): 
                    $restante = $fila['cantidad_disponible'] - $fila['cantidad_necesaria'];
                    $color_stock = $restante < 0 ? 'style="color:red; font-weight:bold;"' : '';
                ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['insumo']) ?></td>
                        <td><?= htmlspecialchars($fila['cantidad_necesaria']) ?></td>
                        <td><?= htmlspecialchars($fila['cantidad_disponible']) ?></td>
                        <td <?= $color_stock ?>><?= htmlspecialchars($restante) ?></td>
                        <td><?= htmlspecialchars($fila['unidad']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>  
                <button type="submit" name="accion" value="confirmar" class="boton-confirmar">Confirmar y Reservar Stock</button></form>
            <?php else: ?>
                <p class="mensaje-error">No hay stock suficiente para realizar esta receta. Por favor, actualiza tu inventario.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['ultima_reserva'])): ?>
            <div class="cancelar-container">
                <p>¿Te equivocaste? Puedes cancelar la última reserva realizada.</p>
                <form method="POST" action=""><button type="submit" name="accion" value="cancelar" class="boton-cancelar">Cancelar Última Reserva</button></form>
            </div>
        <?php endif; ?>

         </body>
</html>