<?php
    function active($mod) {
        global $activePage;
        return $activePage === $mod ? 'active bg-primary' : '';
    }

    // Detecta si estamos en un módulo (subcarpeta) o en la raíz
    $base = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '.' : 'modulos';
    $logoutBase = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '../PHP/logout.php' : 'PHP/logout.php';
    $dashboardBase = (strpos($_SERVER['PHP_SELF'], '/modulos/') !== false) ? '../dashboard.php' : 'dashboard.php';

    $isVentas = $_SESSION['UserType'] === 'ventas';
?>

<nav id="sidebar" class="bg-dark text-white flex-shrink-0 p-3 d-flex flex-column" style="width: 250px; min-height: 100vh; transition: width 0.3s;">
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
            <a id="ventas" href="<?= $base ?>/ventas.php" class="nav-link text-white <?= active('ventas') ?>">
                <i class="bi bi-cart me-2"></i> <span class="sidebar-text">Ventas</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="reportes" href="<?= $base ?>/reportes.php" class="nav-link text-white <?= active('reportes') ?>">
                <i class="bi bi-bar-chart me-2"></i> <span class="sidebar-text">Reportes</span>
            </a>
        </li>
        <li class="nav-item mb-2">
            <a id="productos" href="<?= $base ?>/productos.php" class="nav-link text-white <?= active('productos') ?>">
                <i class="bi bi-box-seam me-2"></i> <span class="sidebar-text">Productos</span>
            </a>
        </li>
        <li class="nav-item mb-2 <?= $isVentas ? 'd-none' : '' ?>">
            <a id="clientes" href="<?= $base ?>/clientes.php" class="nav-link text-white <?= active('clientes') ?>">
                <i class="bi bi-people me-2"></i> <span class="sidebar-text">Clientes</span>
            </a>
        </li>
        <li class="nav-item mb-2 <?= $isVentas ? 'd-none' : '' ?>">
            <a id="proveedores" href="<?= $base ?>/proveedores.php" class="nav-link text-white <?= active('proveedores') ?>">
                <i class="bi bi-truck me-2"></i> <span class="sidebar-text">Proveedores</span>
            </a>
        </li>
        <li class="nav-item mb-2 <?= $isVentas ? 'd-none' : '' ?>">
            <a id="usuarios" href="<?= $base ?>/usuarios.php" class="nav-link text-white <?= active('usuarios') ?>">
                <i class="bi bi-people me-2"></i> <span class="sidebar-text">Usuarios</span>
            </a>
        </li>
        <li class="nav-item my-3">
            <a id="cerrar" href="<?= $logoutBase ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> <span class="sidebar-text">Cerrar Sesión</span>
            </a>
        </li>
    </ul>

<script>
    const dbUsed = "<?php echo (isset($_SESSION['dbUsed']) && $_SESSION['dbUsed'] === 'remote') ? 'remote' : 'local'; ?>";
    console.log("Valor inicial de dbUsed:", dbUsed);
</script>

    <div class="mt-auto"> 
        <div class="card bg-dark border-0 text-white py-2 px-3"> 
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <i class="bi bi-database me-2"></i>
                    <span class="mb-0">Base de Datos</span>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="databaseSwitch" value="<?php echo (isset($_SESSION['dbUsed']) && $_SESSION['dbUsed'] === 'remote') ? 'remote' : 'local'; ?>"
    <?php echo (isset($_SESSION['dbUsed']) && $_SESSION['dbUsed'] === 'remote') ? 'checked' : ''; ?>>
                </div>
            </div>
            <div class="d-flex align-items-center mb-1">
                <i id="connectionIcon" class="bi bi-globe me-2"></i>
                <span class="fw-bold" id="connectionTypeDisplay">Local</span>
                <i id="connectionStatusDot" class="bi bi-circle-fill text-success ms-2" style="font-size: 0.6em;"></i>
            </div>
            <small class="" id="connectionDetailsDisplay">Almacenamiento local</small>
            </div>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="/ferreteria/CSS/sidebar.css">

<script>
    // Sidebar toggle
    

    $('#toggleSidebar').on('click', function() {
        dinamicDB();
    });

    function dinamicDB(){
        $('#sidebar').toggleClass('collapsed');
        $('.sidebar-text').toggleClass('d-none');

        const databaseTextElementsToHide = $('#databaseSwitch').closest('.card').find('span:not(#connectionTypeDisplay), small, input');
        const databaseBaseText = $('#databaseSwitch').closest('.d-flex').find('span:first'); // Asume que "Base de Datos" es el primer span en el d-flex
        const databaseSwitchContainer = $('#databaseSwitch').closest('.form-switch'); // El contenedor del switch
        
        // Elementos específicos para mover/mostrar/ocultar
        const connectionIcon = $('#connectionIcon'); // El icono principal
        const connectionStatusDot = $('#connectionStatusDot'); // La bolita de estado

        const databaseTextElements = $('#databaseSwitch').closest('.card').find('span, small, input, #connectionStatusDot');

        if ($('#sidebar').hasClass('collapsed')) {
            $('#title-side').addClass('d-none');
            $('#sidebar').css('width', '80px');
            $('#mainContent').css('margin-left', '70px');

            databaseTextElements.addClass('d-none');
            // Asegúrate de que el switch también pueda ser afectado si su texto no se oculta con el `span`
            $('#databaseSwitch').closest('.form-switch').find('span').addClass('d-none'); // Ocultar el span "Base de Datos" si es necesario
            $('#databaseSwitch').closest('.form-switch').find('small').addClass('d-none'); // Ocultar el small de detalles si es necesario

            connectionStatusDot.removeClass('d-none'); // Asegura que esté visible
            connectionStatusDot.css({'position': 'absolute','top': connectionIcon.position().top + connectionIcon.outerHeight() - (connectionStatusDot.outerHeight() / 2),'left': connectionIcon.position().left + (connectionIcon.outerWidth() / 2) - (connectionStatusDot.outerWidth() / 2),'margin-left': '0'});
        } else {
            $('#title-side').removeClass('d-none');
            $('#sidebar').css('width', '250px');
            $('#mainContent').css('margin-left', '0');

            databaseTextElements.removeClass('d-none');
            $('#databaseSwitch').closest('.form-switch').find('span').removeClass('d-none');
            $('#databaseSwitch').closest('.form-switch').find('small').removeClass('d-none');
            connectionStatusDot.css({'position': '','top': '','left': '','margin-left': '0.5rem'});
        }
    }
</script>

<script>
    let dbChoice;

    $(document).ready(function () {
        // Initial setup when the page loads
            updateDatabaseDisplay();

    // Event listener for the databaseSwitch
    $('#databaseSwitch').on('change', function () {
        updateDatabaseDisplay();
        // You can add other logic here that depends on dbChoice changing
    });

    // Function to update the display and the dbChoice variable
    function updateDatabaseDisplay() {
        const isRemoteSelected = $('#databaseSwitch').is(':checked'); // Check if the switch is ON (remote)

        const connectionIcon = $("#connectionIcon");
        const connectionTypeDisplay = $("#connectionTypeDisplay");
        const connectionStatusDot = $("#connectionStatusDot");
        const connectionDetailsDisplay = $("#connectionDetailsDisplay");

        if (isRemoteSelected) {
            dbChoice = 'remote';

            // Update for Remote
            connectionIcon.removeClass("bi-globe").addClass("bi-cloud"); // Changed icon to cloud
            connectionTypeDisplay.text("Remoto");
            connectionStatusDot.removeClass("text-success").addClass("text-primary"); // Dot color blue
            connectionDetailsDisplay.text("Almacenamiento remoto").removeClass("text-success").addClass("text-primary");
            $('#databaseSwitch').val('remote');
        } else {
            dbChoice = 'local';

            // Update for Local
            connectionIcon.removeClass("bi-cloud").addClass("bi-globe"); // Changed icon to globe
            connectionTypeDisplay.text("Local");
            connectionStatusDot.removeClass("text-primary").addClass("text-success"); // Dot color green
            connectionDetailsDisplay.text("Almacenamiento local").removeClass("text-primary").addClass("text-success");
            $('#databaseSwitch').val('local');
        }

        $.post('/ferreteria/PHP/setSession.php', { dbUsed: dbChoice });

        console.log("Current dbChoice:", dbChoice); // For debugging
    }
    });
</script>