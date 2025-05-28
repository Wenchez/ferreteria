<?php
session_start(); // Siempre inicia la sesión al principio

// Verifica si la sesión 'UserID' NO existe
if (!isset($_SESSION['UserID'])) {
    // Si no está loggeado, redirige a login.php
    header("Location: login.php");
    exit(); // Es crucial llamar a exit() después de header()
}

$isVentas = $_SESSION['UserType'] === 'ventas';
// Si la sesión 'UserID' existe, el usuario está loggeado
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ferretería</title>
    <?php include "components/header.html" ?>
</head>
<body class="bg-light d-flex">
        <!-- Sidebar -->
        <?php
            $activePage = 'dashboard';
            include_once "components/sidebar.php";
        ?>
        <!-- Main Content -->
        <div id="mainContent" class="flex-grow-1 p-4" style="transition: margin-left 0.3s;">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h1 class="display-5 fw-bold">¡Bienvenido a la Ferretería!</h1>
                        <p class="lead">Gestiona tus productos, ventas y clientes desde este panel principal.</p>
                    </div>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-success">
                                <div class="mb-3">
                                    <i class="bi bi-cart fs-1"></i>
                                </div>
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text">Registra una nueva venta.</p>
                                <a href="modulos/ventas.php" class="btn btn-outline-success btn-sm">Ver ventas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-secondary">
                                <div class="mb-3">
                                    <i class="bi bi-bar-chart fs-1"></i>
                                </div>
                                <h5 class="card-title">Reportes</h5>
                                <p class="card-text">Consulta y gestiona las ventas realizadas</p>
                                <a href="modulos/reportes.php" class="btn btn-outline-secondary btn-sm">Ver Reportes</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 g-4 justify-content-center">
                    <div class="col-md-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-primary">
                                <div class="mb-3">
                                    <i class="bi bi-box-seam fs-1"></i>
                                </div>
                                <h5 class="card-title">Productos</h5>
                                <p class="card-text">Administra el inventario de productos de la tienda.</p>
                                <a href="modulos/productos.php" class="btn btn-outline-primary btn-sm">Ver productos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 <?= $isVentas ? 'd-none' : '' ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-info">
                                <div class="mb-3">
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text">Gestiona la información de tus clientes.</p>
                                <a href="modulos/clientes.php" class="btn btn-outline-info btn-sm">Ver clientes</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 <?= $isVentas ? 'd-none' : '' ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-warning">
                                <div class="mb-3">
                                    <i class="bi bi-truck fs-1"></i>
                                </div>
                                <h5 class="card-title">Proveedores</h5>
                                <p class="card-text">Gestiona la información de tus proveedores.</p>
                                <a href="modulos/proveedores.php" class="btn btn-outline-warning btn-sm">Ver proveedores</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 <?= $isVentas ? 'd-none' : '' ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center text-purple">
                                <div class="mb-3">
                                    <i class="bi bi-person fs-1"></i>
                                </div>
                                <h5 class="card-title">Usuarios</h5>
                                <p class="card-text">Gestiona a los usuarios.</p>
                                <a href="modulos/usuarios.php" class="btn btn-outline-purple btn-sm">Gestiona a los usuarios</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col text-center">
                        <small class="text-muted">&copy; <?php echo date('Y'); ?> Ferretería. Todos los derechos reservados.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>