<?php
session_start();

// Solo los administradores pueden ver esta página
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset("utf8");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para obtener los insumos ordenados por la última actualización (los más recientes primero)
// LIMIT 50 para no sobrecargar la página si tienes muchos insumos. Puedes ajustar este número.
$sql = "SELECT nombre, cantidadActual, precio_presentacion_compra, fecha_vencimiento, ultima_actualizacion 
        FROM insumos 
        WHERE ultima_actualizacion IS NOT NULL
        ORDER BY ultima_actualizacion DESC 
        LIMIT 50";

$resultado = $conexion->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Insumos</title>
    <link rel="stylesheet" href="../css/style.css" /> <link rel="stylesheet" href="../css/historial.css"> </head>
<body>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" /></a>
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
                    <li><a href="historial.php">Historial de Insumos</a></li> 
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

    <div class="container">
        <h1>Historial de Actualizaciones de Insumos</h1>
        <p>Mostrando los 50 cambios más recientes.</p>

        <table class="historial-table">
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Última Actualización</th>
                    <th>Stock Actual (g)</th>
                    <th>Precio por Kilo</th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
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
                            <td>$<?php echo number_format($fila['precio_presentacion_compra'], 0, ',', '.'); ?></td>
                            <td class="fecha">
                                <?php 
                                    if (!empty($fila['fecha_vencimiento'])) {
                                        $vencimiento = new DateTime($fila['fecha_vencimiento']);
                                        echo $vencimiento->format('d/m/Y');
                                    } else {
                                        echo 'No registrada';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No hay registros de actualizaciones para mostrar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conexion->close();
?>