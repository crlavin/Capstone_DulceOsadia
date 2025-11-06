<?php

require '../config/database.php';
require '../config/config.php';
require 'clienteFunciones.php';


$errors = [];

$db = new Database();
$con = $db->conectar();

if (!empty($_POST)) {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $rut = trim($_POST['rut']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $rut, $usuario, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (esEmail([$email])) {
        $errors[] = "La dirección de correo no es válida";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (existeUsuario($usuario, $con)) {
        $errors[] = "El nombre del usuario $usuario ya existe";
    }

    if (emailExiste($email, $con)) {
        $errors[] = "El correo electrónico $email ya existe";
    }

    if (count($errors) == 0) {

        $id = registraCliente([$nombres, $apellidos, $email, $telefono, $rut], $con);
        if ($id > 0) {

            require 'Mailer.php';
            $mailer = new Mailer();
            $token = generarToken();
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);

            $idUsuario = registraUsuario([$usuario, $pass_hash, $token, $id], $con);
            if ($idUsuario > 0) {

                $url = SITE_URL . '/activa_cliente.php?id=' . $idUsuario . '&token=' . $token;
                //http://localhost/Dulce_Osadia/activa_cliente.php?id=2&token=eb04b0053eda1b5957b3866227cd0ea6
                $asunto = "Activar cuenta - Dulce Osadia";
                $cuerpo = "Estimado $nombres: <br> Para continuar con el proceso de registro haga click 
                en el siguiente enlace <a href='$url'>Activar cuenta</a>.";

                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    echo "Estimado $nombres: <br> Para terminar el proceso de registro siga las instrucciones que le hemos enviado a la 
                    dirección de correo electrónico $email";

                    exit;
                }
            } else {
                $errors[] = "Error al regitrar usuario";
            }
        } else {
            $errors[] = "Error al regitrar cliente";
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
            margin: 0;
            padding: 0;
            background: #c41f3d;
            background-image: url("../img/Patrones_celestes/Recurso_105.png");
            background-repeat: repeat;
            background-size: cover;
            background-position: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .text-danger {
            color: red;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button.realizar-registro-btn {
            background-color: #A0C3D2;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button.realizar-registro-btn:hover {
            background-color: #A0C3D2;
        }

        .note {
            text-align: center;
            margin: 10px 0;
            color: #555;
        }

        .alerta {
            padding: 20px;
            background-color: #A0C3D2;
            /* Rojo */
            color: blanco;
            margin-bottom: 15px;
            position: relative;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cerrar {
            margin-left: 15px;
            color: blanco;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .cerrar:hover {
            color: negro;
        }
    </style>
</head>

<body>
    <?php include 'menu.php'; ?>

    <main>
        <div class="container">
            <h2>Datos del cliente</h2>

            <?php mostrarErrores($errors); ?>

            <form class="row g-3" action="registro.php" method="post" autocomplete="off">
                <div class="col-md-6">
                    <label for="nombres"><span class="text-danger">*</span>Nombres</label>
                    <input type="text" name="nombres" id="nombres" class="form-control" required>
                </div><br>
                <div class="col-md-6">
                    <label for="apellidos"><span class="text-danger">*</span>Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                </div><br>
                <div class="col-md-6">
                    <label for="email"><span class="text-danger">*</span>Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <span id="validaEmail" class="text-danger"></span>
                </div><br>
                <div class="col-md-6">
                    <label for="telefono"><span class="text-danger">*</span>Telefono</label>
                    <input type="tel" name="telefono" id="telefono" class="form-control" required>
                </div><br>
                <div class="col-md-6">
                    <label for="rut"><span class="text-danger">*</span>Rut</label>
                    <input type="text" name="rut" id="rut" class="form-control" required>
                </div><br>
                <div class="col-md-6">
                    <label for="usuario"><span class="text-danger">*</span>Usuario</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" required>
                    <span id="validaUsuario" class="text-danger"></span>
                </div><br>
                <div class="col-md-6">
                    <label for="password"><span class="text-danger">*</span>Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div><br>
                <div class="col-md-6">
                    <label for="repassword"><span class="text-danger">*</span>Repetir Contraseña</label>
                    <input type="password" name="repassword" id="repassword" class="form-control" required>
                </div><br>

                <i><b>Nota:</b> Los campos con astericos son obligatorios</i>

                <div class="col-12">
                    <br><button type="submit" class="realizar-registro-btn">Registrar</button>
                </div>
            </form>

        </div>
    </main>

    <script>
        let txtUsuario = document.getElementById('usuario')
        txtUsuario.addEventListener("blur", function() {
            existeUsuario(txtUsuario.value)
        }, false)

        let txtEmail = document.getElementById('email')
        txtEmail.addEventListener("blur", function() {
            emailExiste(txtEmail.value)
        }, false)

        function existeUsuario(usuario) {
            let url = "clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeUsuario")
            formData.append("usuario", usuario)

            fetch(url, {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                .then(data => {

                    if (data.ok) {
                        document.getElementById('usuario').value = ''
                        document.getElementById('validaUsuario').innerHTML = 'Usuario no disponible'
                    } else {
                        document.getElementById('validaUsuario').innerHTML = ''
                    }

                })
        }

        function emailExiste(email) {
            let url = "clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "emailExiste")
            formData.append("email", email)

            fetch(url, {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                .then(data => {

                    if (data.ok) {
                        document.getElementById('email').value = ''
                        document.getElementById('validaEmail').innerHTML = 'Correo electrónico no disponible'
                    } else {
                        document.getElementById('validaEmail').innerHTML = ''
                    }

                })
        }
    </script>
</body>
<?php include 'footer.php'; ?>

</html>