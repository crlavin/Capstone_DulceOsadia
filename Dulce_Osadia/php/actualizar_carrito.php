<?php

require '../config/config.php';
require 'config/database.php';

if (isset($_POST['action'])) {

    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    if ($action == 'agregar') {
        $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 0;
        $respuesta = agregar($id, $cantidad);
        // Marcamos ok si se pudo procesar la actualización del carrito
        $datos['ok'] = ($respuesta !== null);
        // Subtotal formateado (si no se pudo calcular, 0)
        $datos['sub'] = MONEDA . number_format($respuesta ?? 0, 0, ',', '.');
    }
} else {
    $datos['ok'] = false;
}

echo json_encode($datos);

function agregar($id, $cantidad)
{
    // Devuelve subtotal calculado o null si no se pudo actualizar
    if ($id > 0 && $cantidad > 0 && is_numeric($cantidad)) {
        if (isset($_SESSION['carrito']['productos'][$id])) {
            $_SESSION['carrito']['productos'][$id] = $cantidad;

            $db = new Database();
            $con = $db->conectar();
            // Usamos el precio mostrado en el checkout: presentaciones_venta.precio_venta
            $sql = $con->prepare("SELECT precio_venta FROM presentaciones_venta WHERE id_producto = ? LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $precio = isset($row['precio_venta']) ? (float)$row['precio_venta'] : 0;
            return $cantidad * $precio;
        }
    }
    return null;
}
