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

// --- FUNCIÓN DE FORMATO DE NÚMERO ---
function smart_number_format($number) {
    if (round($number, 2) == round($number, 0)) {
        return number_format($number, 0, ',', '.');
    }
    return number_format($number, 2, ',', '.');
}

// --- INICIALIZACIÓN DE VARIABLES ---
$mensaje_info = $_SESSION['mensaje_info'] ?? null;
unset($_SESSION['mensaje_info']);

$resultado_plan = null;
$mensaje_error = null;
$stock_suficiente = true;
$audio_a_reproducir = null;
$datos_presentacion = null;
$costo_produccion_total = 0;
$ingreso_total = 0;
$ganancia_estimada = 0;
$total_unidades_a_producir = 0;

// Variables para el nuevo resumen financiero
$costo_por_paquete = 0;
$ganancia_por_paquete = 0;
$margen_por_paquete = 0;


// --- LÓGICA DE ACCIONES ---

// ACCIÓN 1: PLANIFICAR Y VERIFICAR STOCK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'planificar') {
    unset($_SESSION['ultima_reserva']);
    
    $id_presentacion = $_POST["id_presentacion"] ?? null;
    $cantidad_paquetes = $_POST["cantidad_paquetes"] ?? null;

    if ($id_presentacion && is_numeric($cantidad_paquetes) && $cantidad_paquetes > 0) {
        
        // 1. OBTENER DATOS DE LA PRESENTACIÓN, PRODUCTO Y RECETA
        $stmt_presentacion = $conexion->prepare("
            SELECT p.nombre as nombre_producto, p.id_receta, pv.nombre_presentacion, pv.unidades_por_paquete, pv.precio_venta 
            FROM presentaciones_venta pv 
            JOIN producto p ON pv.id_producto = p.id_producto 
            WHERE pv.id_presentacion = ?
        ");
        $stmt_presentacion->bind_param("i", $id_presentacion);
        $stmt_presentacion->execute();
        $datos_presentacion = $stmt_presentacion->get_result()->fetch_assoc();
        $stmt_presentacion->close();

        if ($datos_presentacion && !empty($datos_presentacion['id_receta'])) {
            $id_receta = $datos_presentacion['id_receta'];
            $total_unidades_a_producir = $cantidad_paquetes * $datos_presentacion['unidades_por_paquete'];

            // Activar audio si es el producto correcto
            if ($datos_presentacion['nombre_producto'] === 'Bombón crema de maní') {
                $audio_a_reproducir = 'Bombon';
            }

            // 2. CALCULAR INSUMOS NECESARIOS Y COSTOS
            $sql_insumos = "
                SELECT 
                    i.id_insumo, i.nombre AS insumo, dr.cantidad_usada * ? AS cantidad_necesaria,
                    i.cantidadActual AS cantidad_disponible, dr.unidad,
                    -- ¡CORRECCIÓN! Usamos la nueva columna precio_por_gramos
                    (dr.cantidad_usada * ? * i.precio_por_gramos) AS costo_total_insumo
                FROM detalleReceta dr
                JOIN insumos i ON dr.id_insumo = i.id_insumo
                WHERE dr.id_receta = ?
            ";
            $stmt_insumos = $conexion->prepare($sql_insumos);
            // Usamos $total_unidades_a_producir para ambos cálculos
            $stmt_insumos->bind_param("ddi", $total_unidades_a_producir, $total_unidades_a_producir, $id_receta);
            $stmt_insumos->execute();
            $resultado_plan = $stmt_insumos->get_result();

            $_SESSION['planificacion_actual'] = ['id_presentacion' => $id_presentacion, 'cantidad_paquetes' => $cantidad_paquetes, 'insumos' => [], 'total_unidades' => $total_unidades_a_producir];

            // Este bucle es para calcular totales y verificar stock
            if ($resultado_plan->num_rows > 0) {
                while ($fila = $resultado_plan->fetch_assoc()) {
                    $_SESSION['planificacion_actual']['insumos'][] = $fila;
                    // Sumamos el costo de cada insumo al total de producción
                    $costo_produccion_total += $fila['costo_total_insumo'] ?? 0; // Usar ?? 0 para evitar errores
                    if ($fila['cantidad_disponible'] < $fila['cantidad_necesaria']) {
                        $stock_suficiente = false;
                    }
                }
            } else {
                $mensaje_error = "Este producto no tiene una receta asociada o la receta está vacía.";
                $resultado_plan = null;
            }

            // 3. CALCULAR FINANZAS (SECCIÓN MEJORADA)
            if ($datos_presentacion['precio_venta'] > 0) {
                $ingreso_total = $cantidad_paquetes * $datos_presentacion['precio_venta'];
                $ganancia_estimada = $ingreso_total - $costo_produccion_total;

                // Calcular métricas por paquete individual
                if ($cantidad_paquetes > 0) {
                    $costo_por_paquete = $costo_produccion_total / $cantidad_paquetes;
                }
                $ganancia_por_paquete = $datos_presentacion['precio_venta'] - $costo_por_paquete;
                
                if ($datos_presentacion['precio_venta'] > 0) {
                    $margen_por_paquete = ($ganancia_por_paquete / $datos_presentacion['precio_venta']) * 100;
                }

            } else {
                 if($costo_produccion_total > 0) {
                    $mensaje_error = "El costo de producción es de $" . smart_number_format($costo_produccion_total) . " pero el producto no tiene precio de venta asignado. No se puede calcular la ganancia.";
                 }
            }


        } else {
            $mensaje_error = "La presentación seleccionada no es válida o no tiene receta asignada.";
        }
    } else {
        $mensaje_error = "Por favor, selecciona un producto y una cantidad de paquetes válida.";
    }
}


// ACCIÓN 2: CONFIRMAR Y RESERVAR INSUMOS (Transacción SQL para restar stock)
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

// ACCIÓN 3: CANCELAR LA ÚLTIMA RESERVA (Transacción SQL para sumar stock)
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificador de Producción</title>
    <link rel="stylesheet" href="../css/estilosopcion2.css">
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        .financial-summary {
            background-color: #f0f8ff;
            border-left: 5px solid #4682B4;
            padding: 15px;
            margin-top: 25px;
            border-radius: 5px;
        }
        .financial-summary h2 {
            margin-top: 0;
            color: #2c3e50;
        }
        .financial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .financial-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .financial-card .label {
            font-size: 0.9rem;
            color: #7f8c8d;
            display: block;
        }
        .financial-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
        .value.positive { color: #27ae60; }
        .value.negative { color: #c0392b; }
        .value.neutral { color: #34495e; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><a href="index.php"><img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" /></a></div>
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
                    <li><a href="historial.php">Historial de insumos</a></li> <?php endif; ?>
                <li><a href="logout.php">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="login.php">Iniciar sesión</a></li>
                <li><a href="registro.php">Regístrate</a></li>
            <?php endif; ?>
        </ul>
        <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
    </nav>
    <script src="../js/index.js"></script>
    
    <div class="layout-container">

        <div class="form-container">
            <form method="POST" action="">
                <h2>Planificar Producción</h2>

                <label for="id_presentacion">Producto a fabricar:</label>
                <select id="id_presentacion" name="id_presentacion" required>
                    <option value="">-- Selecciona un formato de venta --</option>
                    <?php
                        $query_presentaciones = "SELECT pv.id_presentacion, pv.nombre_presentacion 
                                                 FROM presentaciones_venta pv
                                                 WHERE pv.estado = 'Activo' ORDER BY pv.nombre_presentacion ASC";
                        $result_presentaciones = $conexion->query($query_presentaciones);
                        while($fila = $result_presentaciones->fetch_assoc()) {
                            $selected = (isset($_POST['id_presentacion']) && $_POST['id_presentacion'] == $fila['id_presentacion']) ? 'selected' : '';
                            echo "<option value='" . $fila['id_presentacion'] . "' $selected>" . htmlspecialchars($fila['nombre_presentacion']) . "</option>";
                        }
                    ?>
                </select>

                <label for="cantidad_paquetes">¿Cuántos paquetes (bolsas/cajas) quieres producir?</label>
                <input type="number" id="cantidad_paquetes" name="cantidad_paquetes" min="1" required value="<?= htmlspecialchars($_POST['cantidad_paquetes'] ?? '1') ?>">

                <button type="submit" name="accion" value="planificar">Planificar</button>
            </form>

            <?php if ($mensaje_info): ?><p class="mensaje-info"><?= $mensaje_info ?></p><?php endif; ?>
            <?php if ($mensaje_error): ?><p class="mensaje-error"><?= $mensaje_error ?></p><?php endif; ?>
        </div>

        <?php if ($resultado_plan && $datos_presentacion): ?>
            <div class="results-container">
                
                <h3>Insumos Requeridos</h3>
                <p>Para producir <strong><?= htmlspecialchars($_POST['cantidad_paquetes'] ?? 0) ?></strong> paquetes de <strong><?= htmlspecialchars($datos_presentacion['nombre_presentacion']) ?></strong> (Total: <strong><?= smart_number_format($total_unidades_a_producir) ?></strong> unidades).</p>

                <div class="insumos-grid">
                    <?php 
                    $resultado_plan->data_seek(0); 
                    while ($fila = $resultado_plan->fetch_assoc()): 
                        $restante = $fila['cantidad_disponible'] - $fila['cantidad_necesaria'];
                        $clase_stock = $restante < 0 ? 'stock-insuficiente' : '';
                    ?>
                        <div class="insumo-card <?= $clase_stock ?>">
                            <h4><?= htmlspecialchars($fila['insumo']) ?></h4>
                            <div class="insumo-detalle">
                                <span class="label">Necesario:</span>
                                <span class="value"><?= smart_number_format($fila['cantidad_necesaria']) ?> <?= htmlspecialchars($fila['unidad']) ?></span>
                            </div>
                            <div class="insumo-detalle">
                                <span class="label">En Bodega:</span>
                                <span class="value"><?= smart_number_format($fila['cantidad_disponible']) ?> <?= htmlspecialchars($fila['unidad']) ?></span>
                            </div>
                            <div class="insumo-detalle stock-restante">
                                <span class="label">Restante:</span>
                                <span class="value"><?= smart_number_format($restante) ?> <?= htmlspecialchars($fila['unidad']) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- NUEVO RESUMEN FINANCIERO -->
                <?php if ($datos_presentacion['precio_venta'] > 0 || $costo_produccion_total > 0): ?>
                <div class="financial-summary">
                    <h2>Resumen Financiero</h2>
                    <div class="financial-grid">
                        <!-- Métricas por Paquete -->
                        <div class="financial-card">
                            <span class="label">Costo por Paquete</span>
                            <span class="value neutral">$<?= smart_number_format($costo_por_paquete) ?></span>
                        </div>
                         <div class="financial-card">
                            <span class="label">Precio Venta Paquete</span>
                            <span class="value positive">$<?= smart_number_format($datos_presentacion['precio_venta']) ?></span>
                        </div>
                        <div class="financial-card">
                            <span class="label">Ganancia por Paquete</span>
                            <span class="value <?= $ganancia_por_paquete >= 0 ? 'positive' : 'negative' ?>">$<?= smart_number_format($ganancia_por_paquete) ?></span>
                        </div>
                        <div class="financial-card">
                            <span class="label">Margen por Paquete</span>
                            <span class="value <?= $margen_por_paquete >= 0 ? 'positive' : 'negative' ?>"><?= smart_number_format($margen_por_paquete) ?>%</span>
                        </div>

                        <!-- Métricas Totales -->
                        <div class="financial-card">
                            <span class="label">Costo Producción Total</span>
                            <span class="value neutral">$<?= smart_number_format($costo_produccion_total) ?></span>
                        </div>
                        <div class="financial-card">
                            <span class="label">Ingreso Total Estimado</span>
                            <span class="value positive">$<?= smart_number_format($ingreso_total) ?></span>
                        </div>
                        <div class="financial-card">
                            <span class="label">Ganancia Total Estimada</span>
                            <span class="value <?= $ganancia_estimada >= 0 ? 'positive' : 'negative' ?>">$<?= smart_number_format($ganancia_estimada) ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>


                <?php if ($stock_suficiente): ?>
                    <form method="POST" action="" style="text-align: center; margin-top: 20px;">
                        <button type="submit" name="accion" value="confirmar" class="boton-confirmar">Confirmar y Reservar Stock</button>
                    </form>
                <?php else: ?>
                    <p class="mensaje-error" style="text-align: center; margin-top: 20px;">No hay stock suficiente para realizar esta producción.</p>
                <?php endif; ?>

                <?php if ($audio_a_reproducir): ?>
                    <div class="audio-player-container">
                        <h4>Narración de la Receta:</h4>
                        <audio controls autoplay>
                            <source src="../audios/<?php echo htmlspecialchars($audio_a_reproducir); ?>.mp3" type="audio/mpeg">
                        </audio>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['ultima_reserva'])): ?>
            <div class="cancelar-container">
                <p>¿Te equivocaste? Puedes cancelar la última reserva realizada.</p>
                <form method="POST" action=""><button type="submit" name="accion" value="cancelar" class="boton-cancelar">Cancelar Última Reserva</button></form>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>

