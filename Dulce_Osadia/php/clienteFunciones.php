<?php

function esNulo($parametros)
{
    foreach ($parametros as $parametro) {
        if (strlen(trim($parametro)) < 1) {
            return true;
        }
    }
    return false;
}

function esEmail($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    return false;
}

function validaPassword($password, $repassword)
{
    if (strcmp($password, $repassword) === 0) {
        return true;
    }
    return false;
}

function generarToken()
{
    return md5(uniqid(mt_rand(), false));
}

function registraCliente(array $datos, $con)
{
    $sql = $con->prepare("INSERT INTO clientes (nombres, apellidos, email, telefono, rut, estatus, fecha_Alta) 
    VALUES (?,?,?,?,?,1, now())");
    if ($sql->execute($datos)) {
        return $con->lastInsertId();
    }
    return 0;
}

function registraUsuario(array $datos, $con)
{
    $sql = $con->prepare("INSERT INTO usuarios (usuario, password, token, id_cliente) 
    VALUES (?,?,?,?)");
    if ($sql->execute($datos)) {
        return $con->lastInsertId();
    }
    return 0;
}

function existeUsuario($usuario, $con)
{
    $sql = $con->prepare("SELECT id FROM usuarios WHERE usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    if ($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}

function emailExiste($email, $con)
{
    $sql = $con->prepare("SELECT id FROM clientes WHERE email LIKE ? LIMIT 1");
    $sql->execute([$email]);
    if ($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}

function mostrarErrores(array $errors)
{
    if (count($errors) > 0) {
        echo '<div class="alerta"><ul>';
        echo '<span class="cerrar" onclick="this.parentElement.style.display=\'none\';">&times;</span>';
        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

function validaToken($id, $token, $con)
{
    $msg = '';
    $sql = $con->prepare("SELECT id FROM usuarios WHERE id = ? AND token LIKE ? LIMIT 1");
    $sql->execute([$id, $token]);
    if ($sql->fetchColumn() > 0) {
        if (activarUsuario($id, $con)) {
            $msg = "Cuenta activada.";
        } else {
            $msg = "Error al activar la cuenta.";
        }
    } else {
        $msg = "No existe el registro del cliente.";
    }
    return $msg;
}

function activarUsuario($id, $con)
{
    $sql = $con->prepare("UPDATE usuarios SET activacion = 1, token = '' WHERE id = ?");
    return $sql->execute([$id]);
}

function login($usuario, $password, $con, $proceso)
{
    // CAMBIO 1: Ahora también pedimos el nombre de usuario (u.usuario) en la consulta
    $sql = $con->prepare("SELECT u.id, u.password, u.id_cliente, c.email, c.nombres, u.usuario 
                          FROM usuarios AS u
                          INNER JOIN clientes AS c ON u.id_cliente = c.id
                          WHERE u.usuario LIKE ? AND u.activacion = 1 LIMIT 1");
    $sql->execute([$usuario]);

    if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            
            // --- ¡LOGIN EXITOSO! ---
            
            $_SESSION['user_id'] = $row['id_cliente'];
            $_SESSION['user_email'] = $row['email'];
            
            // CAMBIO 2: Guardamos el nombre de usuario (de la tabla usuarios) en la sesión
            $_SESSION['user_name'] = $row['usuario'];
            // Alineamos con páginas que usan 'usuario' y 'rol'
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = ($row['usuario'] === 'admin') ? 'admin' : 'usuario';

            $_SESSION['token'] = session_id();

            if ($proceso == 'pago') {
                header("Location: pago.php"); 
            } else if ($_SESSION['rol'] === 'admin') {
                header("Location: inicioadmin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    
    return "El usuario y/o contraseña son incorrectos.";
}

function esActivo($usuario, $con)
{
    $sql = $con->prepare("SELECT activacion FROM usuarios WHERE usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if ($row['activacion'] == 1) {
        return true;
    }
    return false;
}

function solicitaPassword($user_id, $con)
{
    $token = generarToken();

    $sql = $con->prepare("UPDATE usuarios SET token_password=?, password_request=1 WHERE id = ?");
    if ($sql->execute([$token, $user_id])) {
        return $token;
    }
    return null;
}

function verificaTokenRequest($user_id, $token, $con)
{
    $sql = $con->prepare("SELECT id FROM usuarios WHERE id = ? AND token_password LIKE ? AND 
    password_request=1 LIMIT 1");
    $sql->execute([$user_id, $token]);
    if ($sql->fetchColumn() > 0) {
        return true;
    }
    return false;
}

function actualizaPassword($user_id, $password, $con)
{
    $sql = $con->prepare("UPDATE usuarios SET password=?, token_password = '', password_request=0 WHERE id = ?");
    if ($sql->execute([$password, $user_id])) {
        return true;
    }
    return false;
}
