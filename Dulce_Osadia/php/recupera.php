<?php

require '../config/database.php';
require '../config/config.php';
require 'clienteFunciones.php';


$errors = [];

$db = new Database();
$con = $db->conectar();

if (!empty($_POST)) {
    $email = trim($_POST['email']);

    if (esNulo([$email])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (esEmail([$email])) {
        $errors[] = "La dirección de correo no es válida";
    }

    if (count($errors) == 0) {
        if (emailExiste($email, $con)) {
            $sql = $con->prepare("SELECT usuarios.id, clientes.nombres FROM usuarios
            INNER JOIN clientes ON usuarios.id_cliente=clientes.id
             WHERE clientes.email LIKE ? LIMIT 1");
            $sql->execute([$email]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['id'];
            $nombres = $row['nombres'];

            $token = solicitaPassword($user_id, $con);

            if ($token !== null) {
                require 'Mailer.php';
                $mailer = new Mailer();

                $url = SITE_URL . '/reset_password.php?id=' . $user_id . '&token=' . $token;
                $asunto = "Recuperar password - Dulce Osadia";
                $cuerpo = "Estimado $nombres: <br> Si has solicitado el cambio de tu contraseña haga click 
                en el siguiente enlace <a href='$url'>$url</a>.";
                $cuerpo .= "<br> Si no realizaste esta petición puedes ignorar este correo.";

                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    echo "<p><b>Correo enviado</b></p>";
                    echo "<p>Hemos enviado un correo eletrónico a la dirección $email para restablecer la contraseña.
                    </p>";

                    exit;
                }
            }
        } else {
            $errors[] = "No existe una cuenta asociada a esta dirección de correo";
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

        <h1>Recuperar Contraseña</h1>

        <?php if (!empty($errors)) : ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="recupera.php" method="post" autocomplete="off">

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input class="form-control" type=email name="email" id="email" placeholder="Correo electrónico" required>
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
<?php include 'footer.php'; ?>
</html>