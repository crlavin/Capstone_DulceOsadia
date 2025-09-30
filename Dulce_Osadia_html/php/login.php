<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = null;
$success = null;

// Si ya está logueado, redirige
if (isset($_SESSION['usuario'])) {
  header('Location: index.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Conexión
  $conexion = new mysqli("localhost","root","dulceosadia","dulceosadia");
  $conexion->set_charset('utf8mb4');

  $usuario = trim($_POST['usuario'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($usuario === '' || $password === '') {
    $error = 'Completa usuario y contraseña.';
  } else {
    try {
      $sql = "SELECT idusuario, nombre, correo, usuario, password, rol FROM usuarios WHERE usuario = ? LIMIT 1";
      $stmt = $conexion->prepare($sql);
      if ($stmt === false) throw new Exception('Error al preparar consulta: ' . $conexion->error);
      $stmt->bind_param('s', $usuario);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($row = $result->fetch_assoc()) {
        // Verifica si el password está hasheado
        $stored = $row['password'] ?? '';
        if (strpos($stored, '$2y$') !== 0 && strpos($stored, '$argon') !== 0) {
          $error = 'Cuenta con contraseña en formato inseguro. Contacta al administrador.';
        } elseif (password_verify($password, $stored)) {
          session_regenerate_id(true);
          $_SESSION['user_id'] = $row['idusuario'];
          $_SESSION['usuario'] = $row['usuario'];
          $_SESSION['nombre'] = $row['nombre'];
          $_SESSION['rol'] = $row['rol'];
          $success = 'Usuario logueado correctamente';
          // redirigir con pequeño delay para mostrar toast
          echo "<script>window.onload = function(){ localStorage.setItem('toast', " . json_encode($success) . "); window.location.href='index.php'; }</script>";
          exit();
        } else {
          $error = 'Usuario o contraseña incorrectos.';
        }
      } else {
        $error = 'Usuario o contraseña incorrectos.';
      }
      $stmt->close();
    } catch (Exception $e) {
      $error = 'Error en el proceso de login.';
      error_log('Login error: ' . $e->getMessage());
    } finally {
      $conexion->close();
    }
  }
}

// Pasar mensaje PHP a JS
if (isset($error) && $error !== '') {
  echo "<script>var phpErrorMessage = " . json_encode($error) . ";</script>";
} else {
  echo "<script>var phpErrorMessage = null;</script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Dulce Osadía</title>
  <link rel="stylesheet" href="../css/registrologin.css">
  <style>
    /* Toast simple */
    #toast {
      position: fixed; right: 20px; top: 20px;
      padding: 12px 16px; border-radius: 6px; color: #fff;
      font-family: Arial, sans-serif; display: none; z-index:9999;
      box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }
    #toast.success { background: #2e7d32; }
    #toast.error { background: #c62828; }
    .form-container { max-width:400px; margin:40px auto; }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo">
    <a href="index.php"><img src="../img/Perfil_instagram.png" alt="Dulce Osadía" class="logo-img"></a>
  </div>
  <div class="menu-toggle" id="menu-toggle">☰</div>
  <ul class="nav-link" id="nav-link">
    <li><a href="index.php">Inicio</a></li>
    <li><a href="../Productos/catalogo.php">Catálogo</a></li>
    <li><a href="../html/nosotros.html">Sobre Nosotros</a></li>
    <li><a href="../html/carrito.html">Carrito</a></li>
    <li><a href="../html/perfil.html">Perfil</a></li>
    <li><a href="registro.php">Regístrate</a></li>
  </ul>
  <marquee behavior="scroll" direction="left">Bienvenidos a la página oficial de Dulce Osadía!</marquee>
  <script src="../js/index.js"></script>
</nav>

<div class="form-container">
  <h2>Iniciar Sesión</h2>
  <form action="" method="POST" autocomplete="on" id="loginForm">
    <label for="usuario">Usuario</label>
    <input type="text" name="usuario" required>

    <label for="password">Contraseña</label>
    <input type="password" name="password" required>

    <label style="display:block;margin:8px 0">
      <input type="checkbox" name="remember" id="remember"> Recordarme
    </label>

    <button type="submit">Entrar</button>

    <p class="login-link"><a href="registro.php">¿No tienes cuenta? Regístrate</a></p>
  </form>
</div>

<div id="toast"></div>

<script>
  function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.className = type;
    t.classList.add(type === 'success' ? 'success' : 'error');
    t.textContent = msg;
    t.style.display = 'block';
    clearTimeout(window._toastTimeout);
    window._toastTimeout = setTimeout(()=> t.style.display='none',3500);
  }

  // Mostrar error enviado por PHP
  if (typeof phpErrorMessage !== 'undefined' && phpErrorMessage) {
    showToast(phpErrorMessage, 'error');
  }

  // Mostrar toast guardado por redirect (registro/login exitoso)
  const saved = localStorage.getItem('toast');
  if (saved) { showToast(saved, 'success'); localStorage.removeItem('toast'); }

  // Recordarme (almacenar usuario en localStorage)
  const remUser = localStorage.getItem('remember_user');
  if (remUser) {
    const u = document.querySelector('input[name="usuario"]');
    if (u) { u.value = remUser; document.getElementById('remember').checked = true; }
  }

  document.getElementById('loginForm').addEventListener('submit', function(){
    const remember = document.getElementById('remember').checked;
    const user = document.querySelector('input[name="usuario"]').value;
    if (remember) localStorage.setItem('remember_user', user);
    else localStorage.removeItem('remember_user');
  });
</script>

</body>
</html>
