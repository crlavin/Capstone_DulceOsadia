<?php
// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Conexión a la base de datos
  $conexion = new mysqli("localhost", "root", "dulceosadia", "dulceosadia");

  if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
  }

  // Recibe datos del formulario
  $nombre   = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $correo   = $_POST['correo'];
  $usuario  = $_POST['usuario'];
  $raw_password = $_POST['password'];

  // Validación mínima en servidor: mínimo 12 caracteres
  if (strlen($raw_password) < 12) {
    echo "<script>alert('La contraseña debe tener al menos 12 caracteres.'); window.history.back();</script>";
    exit();
  }

  $password = password_hash($raw_password, PASSWORD_DEFAULT);

  // Inserta en la tabla
  $sql = "INSERT INTO usuarios (nombre, correo, usuario, password) VALUES (?, ?, ?, ?)";
  $stmt = $conexion->prepare($sql);
  if ($stmt === false) {
    echo "<script>alert('Error en la preparación de la consulta: " . addslashes($conexion->error) . "'); window.history.back();</script>";
    $conexion->close();
    exit();
  }
  $stmt->bind_param("ssss", $nombre, $correo, $usuario, $password);

  if ($stmt->execute()) {
    echo "<script>alert('✅ Usuario registrado correctamente'); window.location.href='index.php';</script>";
    exit();
  } else {
    echo "<script>alert('❌ Error al registrar: " . addslashes($stmt->error) . "'); window.history.back();</script>";
    exit();
  }

  $stmt->close();
  $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  <link rel="stylesheet" href="../css/registrologin.css">
</head>
<body>

<nav class="navbar">
  <div class="logo">
    <a href="index.php">
      <img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img">
    </a>
  </div>
  <div class="menu-toggle" id="menu-toggle">☰</div>
  <ul class="nav-link" id="nav-link">
    <li><a href="../html/index.html">Inicio</a></li>
    <li><a href="#">Catálogo</a></li>
    <li><a href="#">Sobre Nosotros</a></li>
    <li><a href="#">Carrito</a></li>
    <li><a href="#">Perfil</a></li>
  </ul>
  <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
  <script src="../js/index.js"></script>
</nav>

<div class="form-container">
  <h2>Formulario Registro</h2>
  <form id="registroForm" action="" method="POST" novalidate>
    <label for="nombre">Ingrese su Nombre</label>
    <input type="text" name="nombre" required>

    <label for="apellido">Ingrese su Apellido</label>
    <input type="text" name="apellido" required>

    <label for="correo">Ingrese su Correo</label>
    <input type="email" name="correo" required>

    <label for="usuario">Ingrese su Usuario</label>
    <input type="text" name="usuario" required>

    <label for="password">Ingrese su Contraseña</label>
    <input type="password" id="password" name="password" minlength="12" required>

    <small id="pwHelp" style="display:block;color:#666;margin-bottom:10px">
      Mínimo 12 caracteres.
    </small>

    <button type="submit">Registrar</button>

    <p class="login-link"><a href="login.php">¿Ya tengo Cuenta?</a></p>
  </form>
</div>

<script>
  // Validación cliente: solo mínimo 12 caracteres
  document.getElementById('registroForm').addEventListener('submit', function(e) {
    const pwd = document.getElementById('password').value || '';
    if (pwd.length < 12) {
      e.preventDefault();
      alert('La contraseña debe tener al menos 12 caracteres.');
      return false;
    }
    // si pasa, se envía y el servidor volverá a validar
  });
</script>

</body>
</html>
