<?php

require 'config/database.php';
require '../config/config.php';
require 'clienteFunciones.php';

$user_id = $_GET['id'] ?? $_POST['user_id'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($user_id == '' || $token == '') {
    header("Location: index.php");
    exit;
}

$errors = [];

$db = new Database();
$con = $db->conectar();

if (!verificaTokenRequest($user_id, $token, $con)) {
    echo "No se pudo verificar la información";
    exit;
}

if (!empty($_POST)) {

    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$user_id, $token, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (count($errors) == 0) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        if (actualizaPassword($user_id, $pass_hash, $con)) {
            echo "Contraseña modificada.<br><a href='login.php'>Iniciar sesión</a>";
            exit;
        } else {
            $errors[] = "Error al modificar la contraseña. Intentalo nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Dulce Osadia</title>
    <meta name="description" content="Tienda en línea de chocolates.">
    <meta name="keywords" content="dulce osadia, tienda de chocolates">
    <link rel="stylesheet" type="text/css" href="../css/estilos.css">
    <link rel="stylesheet" type="text/css" href="../css/normalize.css">
    <link rel="stylesheet" type="text/css" href="../css/mobile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #c41f3d;
            background-image: url("../img/Patrones_celestes/Recurso_105.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .text-danger {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        button.realizar-recuperación-btn {
            background-color: #A0C3D2;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            border-radius: 4px;
            width: 100%;
            padding: 10px;
        }

        button.realizar-recuperación-btn:hover {
            background-color: #A0C3D2;
        }

        .note {
            text-align: center;
            margin-top: 15px;
            color: #555;
        }

        .error-messages {
            margin-bottom: 15px;
        }

        .error-messages p {
            color: red;
            margin: 5px 0;
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
        }

        .error-messages li {
            color: red;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main class="container">

        <h1>Cambiar Contraseña</h1>

        <?php if (!empty($errors)) : ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="reset_password.php" method="post" autocomplete="off">

            <input type="hidden" name="user_id" id="user_id" value="<?= $user_id; ?>" />
            <input type="hidden" name="token" id="token" value="<?= $token; ?>" />

            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input class="form-control" type=password name="password" id="password" placeholder="Nueva Contraseña" required>
            </div>

            <div class="form-group">
                <label for="repassword">Confirmar Contraseña</label>
                <input class="form-control" type=password name="repassword" id="repassword" placeholder="Confirmar Contraseña" required>
            </div>

            <div>
                <br><button type="submit" class="realizar-recuperación-btn">Aceptar</button>
            </div>
            <hr>

            <div class="note">
                ¿No tienes cuenta? <a href="registro.php">Registrate aquí</a>
            </div>
        </form>

    </main>

</body>

</html>