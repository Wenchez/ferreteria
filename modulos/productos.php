<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.php");
    exit();
}

$isVentas = $_SESSION['UserType'] === 'ventas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Ferretería</title>
    <?php include "../components/header.html" ?>
    <script src="../JS/productos/addProductos.js"></script>
    <script src="../JS/productos/getProductos.js"></script>
    <script src="../JS/productos/modProductos.js"></script>
    <script src="../JS/productos/deleteProductos.js"></script>

    <script>
        const userType = "<?= $_SESSION['UserType'] ?>";
    </script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php 
    $activePage = 'productos';
    include_once "../components/sidebar.php"; 
    ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="text-primary"><i class="bi bi-box-seam"></i> Productos</h2>
                    <button id="add_Product" class="btn btn-primary d-flex align-items-center <?= $isVentas ? 'd-none' : '' ?>" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus me-2"></i> Añadir producto
                    </button>
                </div>
            </div>
            
            <!-- Alerta de stock bajo -->
            <div class="row">
                <div id='alert_stock'></div>
            </div>
            <!-- Tabla de productos -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h2 class="card-title text-primary"><i class="bi bi-receipt"></i> Inventario</h2>
                            <table class="table table-bordered align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Stock</th>
                                    <th scope="col">Proveedor</th>
                                    <th scope="col">Estado</th>
                                    <th class="<?= $isVentas ? 'd-none' : '' ?>" scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de crear producto -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Añadir Nuevo Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addProductForm">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" id="productName" name="productName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoría</label>
                                    <input type="text" class="form-control" id="category" name="category" required>
                                </div>
                                <div class="mb-3">
                                    <label for="supplierName" class="form-label">Nombre del Proveedor</label>
                                    <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required min="0">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="addProductForm">Guardar Producto</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modProductModal" tabindex="-1" aria-labelledby="modProductModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modProductModalLabel">Modificar Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modProductForm">
                                <input type="hidden" id="editId" name="editId">
                                <div class="mb-3">
                                    <label for="editProductName" class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" id="editProductName" name="editProductName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCategory" class="form-label">Categoría</label>
                                    <input type="text" class="form-control" id="editCategory" name="editCegory" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSupplierName" class="form-label">Nombre del Proveedor</label>
                                    <input type="text" class="form-control" id="editSupplierName" name="editSupplierName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editStock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="editStock" name="editStock" required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="editPrice" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="editPrice" name="editPrice" step="0.01" required min="0">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="modProductForm">Guardar Producto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>