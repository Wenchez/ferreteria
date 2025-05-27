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
    <title>Clientes - Ferretería</title>

    <?php include "../components/header.html" ?>

    <script src="../JS/clientes/addClientes.js"></script>
    <script src="../JS/clientes/getClientes.js"></script>
    <script src="../JS/clientes/modClientes.js"></script>
    <script src="../JS/clientes/deleteClientes.js"></script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php 
    $activePage = 'clientes';
    include_once "../components/sidebar.php"; 
    ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="text-info"><i class="bi bi-people"></i> Clientes</h2>
                    <button id="add_Client" class="btn btn-info d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addClientModal">
                        <i class="bi bi-plus me-2"></i> Añadir cliente
                    </button>
                </div>
            </div>
            
            <!-- Tabla de clientes -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-info"><i class="bi bi-people"></i> Lista de Clientes</h2>
                            <table class="table table-bordered align-middle">
                                <thead class="table-info">
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Correo</th>
                                        <th scope="col">Dirección</th>
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
            <!-- Modal de crear cliente -->
            <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addClientModalLabel">Añadir Nuevo Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addClientForm">
                                <div class="mb-3">
                                    <label for="clientName" class="form-label">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="clientName" name="clientName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="addClientForm">Guardar Cliente</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de modificar cliente -->
            <div class="modal fade" id="modClientModal" tabindex="-1" aria-labelledby="modClientModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modClientModalLabel">Modificar Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modClientForm">
                                <input type="hidden" id="editId" name="editId">
                                <div class="mb-3">
                                    <label for="editClientName" class="form-label">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="editClientName" name="editClientName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="editPhone" name="editPhone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="editEmail" name="editEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editAddress" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="editAddress" name="editAddress" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="modClientForm">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>