<?php
session_start();
// Configuración de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// --- 1. CONEXIÓN A LA BASE DE DATOS ---
$conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");
$conexion->set_charset('utf8mb4');

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// --- 2. CONSULTA SQL PARA OBTENER UN PRODUCTO ESPECÍFICO ---
// Vamos a obtener el producto con id_presentacion = 2 ("Nuez Rellena - unidad")
$id_producto_a_mostrar = 2; 

$stmt = $conexion->prepare("SELECT * FROM presentaciones_venta WHERE id_presentacion = ? AND estado = 'Activo'");
$stmt->bind_param("i", $id_producto_a_mostrar);
$stmt->execute();
$resultado = $stmt->get_result();

$product = null;

if ($fila = $resultado->fetch_assoc()) {
    // Si encontramos el producto, creamos el array $product con los datos de la base de datos
    $product = [
        'id_presentacion' => $fila['id_presentacion'],
        'title' => htmlspecialchars($fila['nombre_presentacion']),
        'subtitle' => 'Unidades por paquete: ' . $fila['unidades_por_paquete'], // Usamos un dato de la tabla como subtítulo
        'price' => $fila['precio_venta'],
        'sku' => htmlspecialchars($fila['SKU']),
        'category' => 'Nueces', // Asignado manualmente o buscar en otra tabla
        'image' => '../img/Recursosrecetasimg/3.png', // Deberías cambiar esto por la imagen correcta de la nuez
        'available' => ($fila['estado'] === 'Activo'),
    ];
}

$stmt->close();
$conexion->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dulce Osadía</title>
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<style>
  body {
    background-color: #fed794;
    background-image: url("../img/Patrones_rosados/Recurso_108.png");
    background-repeat: repeat;
    background-size: cover;
    background-position: center;
  }
</style>
    <nav class="navbar">
    <div class="logo">
      <a href="../php/index.php">
        <img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img" />
      </a>
    </div>

    <div class="menu-toggle" id="menu-toggle">☰</div>

    <ul class="nav-link" id="nav-link">
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
  <li><a href="gestion_admin.php">Panel de Gestión</a></li>
  <?php endif; ?>
  <li><a href="../php/index.php">Inicio</a></li>
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
  
<?php
// Solo incluimos la plantilla si se encontró el producto en la base de datos
if ($product) {
    include_once '../php/product_template.php';
} else {
    echo "<p style='text-align: center; margin-top: 50px;'>Producto no encontrado o inactivo.</p>";
}
?>
</body>
</html>