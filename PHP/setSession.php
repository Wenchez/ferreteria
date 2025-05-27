<?php
session_start();

if (isset($_POST['dbUsed'])) {
    $_SESSION['dbUsed'] = $_POST['dbUsed'];
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parámetro "dbUsed" faltante']);
}
?>