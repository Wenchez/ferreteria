<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas - Ferretería</title>
    <?php include "../components/header.html" ?>
    <script src="../JS/ventas/addVentas.js"></script>
    <script src="../JS/ventas/getProd.js"></script>
    <script src="../JS/ventas/cleanVentas.js"></script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php
        $activePage = 'ventas';
        include_once "../components/sidebar.php";
    ?>
    <!-- Main Content -->
     <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="mb-4 text-success"><i class="bi bi-cart"></i> Ventas</h2>
                    <button id="clean" class="btn btn-outline-danger d-flex align-items-center">
                        <i class="bi bi-trash"></i> Limpiar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h2 class="text-info card-title"><i class="bi bi-person-fill"></i> Información del Cliente</h2>
                            <div class="my-3">
                                <input type="text" class="form-control bg-info-subtle" placeholder="Ingresa el nombre de un cliente" aria-label="Cliente" id="clientSearchInput">
                            </div>
                            <!-- <div class="alert alert-info" role="alert">
                                Cliente: <span id="clientNameDisplay"></span>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-9">
                    <div class="row">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h2 class="text-primary card-title"><i class="bi bi-box-seam-fill"></i> Agregar Productos</h2>
                                    <div class="input-group my-3">
                                        <input id="add_prod" type="text" class="form-control bg-primary-subtle" placeholder="Agregar producto por nombre..." aria-label="Buscar producto por nombre" id="productSearchByNameInput">
                                        <button id="prod_search" class="btn btn-outline-primary" type="button" id="scanBarcodeButton">Buscar</button>
                                    </div>
                                    <div id='alert_buscar' class="alert alert-warning d-none" role="alert"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h2 class="text-success card-title"><i class="bi bi-bag"></i></i> Productos de la Venta</h2>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-success">
                                                <tr>
                                                    <th scope="col">Código</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col">Precio</th>
                                                    <th scope="col">Cantidad</th>
                                                    <th scope="col">Subtotal</th>
                                                    <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="saleItemsTableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card shadow">
                        <div class="card-body">
                            <h2 class="card-title"><i class="bi bi-receipt-cutoff"></i> Resumen de Venta</h2>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="summarySubtotal"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>IVA (16%):</span>
                                <span id="summaryIva"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 fw-bold">
                                <span>Total:</span>
                                <span id="summaryTotal"></span>
                            </div>
                            <button class="btn btn-outline-secondary w-100 mb-2 d-none" id="summaryProductsCount"></button>
                            <button class="btn btn-outline-secondary w-100 mb-3 d-none" id="summaryUnitsCount"></button>
                            <button class="btn btn-outline-dark w-100" id="processPaymentButton">Procesar Pago</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
        </div>
    </div>
</body>
</html>