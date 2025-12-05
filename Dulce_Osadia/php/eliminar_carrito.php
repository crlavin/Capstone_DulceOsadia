<?php
require_once 'config/config.php';
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    if (isset($_SESSION['carrito']['productos'][$id])) {
        unset($_SESSION['carrito']['productos'][$id]);
    }

    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false]);
}
?>
