<?php
require_once 'config/config.php';

// Aceptar tanto 'id' como 'id_producto' para mayor compatibilidad
if (isset($_POST['id']) || isset($_POST['id_producto'])) {

    $id = isset($_POST['id']) ? $_POST['id'] : $_POST['id_producto'];
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 1;
    $token = $_POST['token'] ?? '';

    $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

    if ($token == $token_tmp && $cantidad > 0 && is_numeric($cantidad)) {
        if (isset($_SESSION['carrito']['productos'][$id])) {
            $_SESSION['carrito']['productos'][$id] += $cantidad;
        } else {
            $_SESSION['carrito']['productos'][$id] = $cantidad;
        }
        // Devolver total de unidades en el carrito (suma de cantidades)
        $datos['numero'] = array_sum($_SESSION['carrito']['productos']);
        $datos['ok'] = true;
    } else {
        $datos['ok'] = false;
    }
} else {
    $datos['ok'] = false;
}

echo json_encode($datos);
