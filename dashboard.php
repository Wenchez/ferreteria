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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ferretería</title>
    <link rel="stylesheet" href="CSS/bootstrap.css">
    <script src="JS/jquery-3.7.1.js"></script>
    <script src="JS/bootstrap.bundle.js"></script>
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white flex-shrink-0 p-3" style="width: 250px; min-height: 100vh; transition: width 0.3s;">
            <div class="d-flex align-items-center mb-4">
                <button class="btn btn-outline-light me-2" id="toggleSidebar" aria-label="Menú">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span class="fs-4 fw-bold">Ferretería</span>
            </div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link active bg-primary text-white">
                        <i class="bi bi-house-door me-2"></i> <span class="sidebar-text">Inicio</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="modulos/productos.php" class="nav-link text-white">
                        <i class="bi bi-box-seam me-2"></i> <span class="sidebar-text">Productos</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="modulos/ventas.php" class="nav-link text-white">
                        <i class="bi bi-cart me-2"></i> <span class="sidebar-text">Ventas</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="modulos/clientes.php" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i> <span class="sidebar-text">Clientes</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="modulos/reportes.php" class="nav-link text-white">
                        <i class="bi bi-bar-chart me-2"></i> <span class="sidebar-text">Reportes</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="modulos/proveedores.php" class="nav-link text-white">
                        <i class="bi bi-truck me-2"></i> <span class="sidebar-text">Proveedores</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a href="PHP/logout.php" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> <span class="sidebar-text">Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Main Content -->
        <div id="mainContent" class="flex-grow-1 p-4" style="transition: margin-left 0.3s;">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col">
                        <h1 class="display-5 fw-bold text-primary">¡Bienvenido a la Ferretería!</h1>
                        <p class="lead">Gestiona tus productos, ventas y clientes desde este panel principal.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-box-seam fs-1 text-warning"></i>
                                </div>
                                <h5 class="card-title">Productos</h5>
                                <p class="card-text">Administra el inventario de productos de la tienda.</p>
                                <a href="modulos/productos.php" class="btn btn-outline-primary btn-sm">Ver productos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-cart fs-1 text-success"></i>
                                </div>
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text">Registra y consulta las ventas realizadas.</p>
                                <a href="modulos/ventas.php" class="btn btn-outline-primary btn-sm">Ver ventas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-people fs-1 text-info"></i>
                                </div>
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text">Gestiona la información de tus clientes.</p>
                                <a href="modulos/clientes.php" class="btn btn-outline-primary btn-sm">Ver clientes</a>
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
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script>
        // Sidebar toggle
        $('#toggleSidebar').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $('.sidebar-text').toggleClass('d-none');
            if ($('#sidebar').hasClass('collapsed')) {
                $('#sidebar').css('width', '70px');
                $('#mainContent').css('margin-left', '70px');
            } else {
                $('#sidebar').css('width', '250px');
                $('#mainContent').css('margin-left', '0');
            }
        });
    </script>
</body>
</html>