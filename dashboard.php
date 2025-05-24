<?php
session_start(); // Siempre inicia la sesión al principio

// Verifica si la sesión 'UserID' NO existe
if (!isset($_SESSION['UserID'])) {
    // Si no está loggeado, redirige a login.html
    header("Location: login.html");
    exit(); // Es crucial llamar a exit() después de header()
}

// Si la sesión 'UserID' existe, el usuario está loggeado
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="JS/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="CSS/bootstrap.css">
    <script src="JS/bootstrap.bundle.js"></script>

    <script src="login.js"></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow-lg text-center" style="max-width: 400px;">
        <h1 class="card-title text-success">Bienvenido al Dashboard</h1>
        <p class="card-text">Has iniciado sesión exitosamente.</p>
        <a href="PHP/logout.php" class="btn btn-danger mt-3">Cerrar Sesión</a>
    </div>
</body>
</html>