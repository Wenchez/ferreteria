<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Ferretería</title>
    <link rel="stylesheet" href="../CSS/bootstrap.css">
    <script src="../JS/jquery-3.7.1.js"></script>
    <script src="../JS/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <a href="../dashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver</a>
        <h2 class="mb-4 text-primary"><i class="bi bi-box-seam"></i> Productos</h2>
        <div class="card">
            <div class="card-body">
                <p class="mb-0">Aquí puedes gestionar el inventario de productos.</p>
                <!-- Aquí iría la tabla/listado de productos y botones para agregar/editar/eliminar -->
            </div>
        </div>
    </div>
</body>
</html>