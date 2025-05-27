
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
    <title>Proveedores - Ferretería</title>
    <?php include "../components/header.html" ?>
    <script src="../JS/proveedores/addProveedores.js"></script>
    <script src="../JS/proveedores/getProveedores.js"></script>
    <script src="../JS/proveedores/modProveedores.js"></script>
    <script src="../JS/proveedores/deleteProveedores.js"></script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php 
    $activePage = 'proveedores';
    include_once "../components/sidebar.php"; 
    ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="text-warning"><i class="bi bi-truck"></i> Proveedores</h2>
                    <button id="add_Supplier" class="btn btn-warning d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                        <i class="bi bi-plus me-2"></i> Añadir proveedor
                    </button>
                </div>
            </div>
            
            <!-- Tabla de proveedores -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-warning"><i class="bi bi-people"></i> Lista de Proveedores</h2>
                            <table class="table table-bordered align-middle">
                                <thead class="table-warning">
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Contacto</th>
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
            <!-- Modal de crear proveedor -->
            <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addSupplierModalLabel">Añadir Nuevo Proveedor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addSupplierForm">
                                <div class="mb-3">
                                    <label for="supplierName" class="form-label">Nombre del Proveedor</label>
                                    <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactName" class="form-label">Nombre de Contacto</label>
                                    <input type="text" class="form-control" id="contactName" name="contactName" required>
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
                            <button type="submit" class="btn btn-primary" form="addSupplierForm">Guardar Proveedor</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de modificar proveedor -->
            <div class="modal fade" id="modSupplierModal" tabindex="-1" aria-labelledby="modSupplierModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modSupplierModalLabel">Modificar Proveedor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modSupplierForm">
                                <input type="hidden" id="editId" name="editId">
                                <div class="mb-3">
                                    <label for="editSupplierName" class="form-label">Nombre del Proveedor</label>
                                    <input type="text" class="form-control" id="editSupplierName" name="editSupplierName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editContactName" class="form-label">Nombre de Contacto</label>
                                    <input type="text" class="form-control" id="editContactName" name="editContactName" required>
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
                            <button type="submit" class="btn btn-primary" form="modSupplierForm">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>