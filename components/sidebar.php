<?php
    function active($mod) {
        global $activePage;
        return $activePage === $mod ? 'active bg-primary' : '';
    }

    // Detecta si estamos en un módulo (subcarpeta) o en la raíz
    $base = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '.' : 'modulos';
    $logoutBase = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '../PHP/logout.php' : 'PHP/logout.php';
    $dashboardBase = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '../dashboard.php' : 'dashboard.php';
?>

<nav id="sidebar" class="bg-dark text-white flex-shrink-0 p-3" style="width: 250px; min-height: 100vh; transition: width 0.3s;">
    <div class="d-flex align-items-center mb-4">
        <button class="btn btn-outline-light me-2" id="toggleSidebar" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span id="title-side" class="fs-4 fw-bold">Ferretería</span>
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a id="inicio" href="<?= $dashboardBase ?>" class="nav-link text-white <?= active('dashboard') ?>">
                <i class="bi bi-house-door me-2"></i> <span class="sidebar-text">Inicio</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="productos" href="<?= $base ?>/productos.php" class="nav-link text-white <?= active('productos') ?>">
                <i class="bi bi-box-seam me-2"></i> <span class="sidebar-text">Productos</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="ventas" href="<?= $base ?>/ventas.php" class="nav-link text-white <?= active('ventas') ?>">
                <i class="bi bi-cart me-2"></i> <span class="sidebar-text">Ventas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="clientes" href="<?= $base ?>/clientes.php" class="nav-link text-white <?= active('clientes') ?>">
                <i class="bi bi-people me-2"></i> <span class="sidebar-text">Clientes</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="reportes" href="<?= $base ?>/reportes.php" class="nav-link text-white <?= active('reportes') ?>">
                <i class="bi bi-bar-chart me-2"></i> <span class="sidebar-text">Reportes</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="proveedores" href="<?= $base ?>/proveedores.php" class="nav-link text-white <?= active('proveedores') ?>">
                <i class="bi bi-truck me-2"></i> <span class="sidebar-text">Proveedores</span>
            </a>
        </li>
        <li class="nav-item mt-3">
            <a id="cerrar" href="<?= $logoutBase ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> <span class="sidebar-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</nav>

<link rel="stylesheet" href="/ferreteria/CSS/sidebar.css">

<script>
    // Sidebar toggle
    $('#toggleSidebar').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
        $('.sidebar-text').toggleClass('d-none');
        if ($('#sidebar').hasClass('collapsed')) {
            $('#title-side').addClass('d-none');
            $('#sidebar').css('width', '80px');
            $('#mainContent').css('margin-left', '70px');
        } else {
            $('#title-side').removeClass('d-none');
            $('#sidebar').css('width', '250px');
            $('#mainContent').css('margin-left', '0');
        }
    });
</script>