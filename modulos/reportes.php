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
    <title>Reportes - Ferretería</title>
    <?php include "../components/header.html" ?>
    <script src="../JS/reportes/getReportes.js"></script>
    <script src="../JS/reportes/deleteReportes.js"></script>

    <script>
        const userType = "<?= $_SESSION['UserType'] ?>";
    </script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php
        $activePage = 'reportes';
        include_once "../components/sidebar.php";
    ?>
    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="text-secondary"><i class="bi bi-bar-chart"></i> Reportes</h2>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h2 class="card-title text-secondary"><i class="bi bi-receipt"></i> Ventas realizadas</h2>

                            <div class="row mb-3">
                                <div class="col-md-9">
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary text-white">Buscar por cliente</span>
                                        <input id="clientName" type="text" class="form-control bg-secondary-subtle" placeholder="Nombre del cliente">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="date" class="form-select bg-secondary-subtle border border-secondary-subtle">
                                        <option value="all" selected>Ver todos</option>
                                        <option value="today">Del día</option>
                                    </select>
                                </div>
                            </div>

                            <table class="table table-bordered align-middle">
                                <thead class="table-secondary">
                                    <tr>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Vendedor</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Productos (cant.)</th>
                                        <th scope="col">Subtotal</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="detailSaleModal" tabindex="-1" aria-labelledby="detailSaleModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailSaleModalLabel">Detalles de venta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 row">
                                <label for="ticketNumber" class="col-sm-4 col-form-label">TICKET:</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="ticketNumber" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="saleDate" class="col-sm-4 col-form-label">FECHA:</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="saleDate" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="saleTime" class="col-sm-4 col-form-label">HORA:</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="saleTime" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="seller" class="col-sm-4 col-form-label">VENDEDOR:</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="seller" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="client" class="col-sm-4 col-form-label">CLIENTE:</label>
                                <div class="col-sm-8">
                                    <input type="text" readonly class="form-control-plaintext" id="client" value="">
                                </div>
                            </div>

                            <hr>

                            <h6>PRODUCTOS:</h6>
                            <div id="productDetailsContainer">
                                </div>

                            <hr>

                            <div class="mb-2 row">
                                <label for="subtotal" class="col-sm-4 col-form-label">SUBTOTAL:</label>
                                <div class="col-sm-8 text-end">
                                    <input type="text" readonly class="form-control-plaintext" id="subtotal" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="iva" class="col-sm-4 col-form-label">IVA (16%):</label>
                                <div class="col-sm-8 text-end">
                                    <input type="text" readonly class="form-control-plaintext" id="iva" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="total" class="col-sm-4 col-form-label fw-bold">TOTAL:</label>
                                <div class="col-sm-8 text-end">
                                    <input type="text" readonly class="form-control-plaintext fw-bold" id="total" value="">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <h5>Compra registrada</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>