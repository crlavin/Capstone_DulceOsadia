<?php

require_once 'config/database.php';
require_once 'clienteFunciones.php';

$datos = [];

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    $db = new Database();
    $con = $db->conectar();

    if ($action == 'existeUsuario') {
        $datos['ok'] = existeUsuario($_POST['usuario'], $con);
    } elseif ($action = 'emailExiste') {
        $datos['ok'] = emailExiste($_POST['email'], $con);
    }
}

echo json_encode($datos);
