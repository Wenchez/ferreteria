<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.php");
    exit();
}

if($_SESSION['UserType'] == 'ventas'){
    header("Location: ../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Ferretería</title>
    <?php include "../components/header.html" ?>
    <script src="../JS/usuarios/getUsuarios.js"></script>
    <script src="../JS/usuarios/modUsuarios.js"></script>
    <script src="../JS/usuarios/deleteUsuarios.js"></script>
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php 
    $activePage = 'usuarios';
    include_once "../components/sidebar.php"; 
    ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="d-flex justify-content-between align-items-center col-12">
                    <h2 class="text-purple"><i class="bi bi-person"></i> Usuarios</h2>
                </div>
            </div>
            
            <!-- Tabla de usuarios -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-purple"><i class="bi bi-person"></i> Lista de Usuarios</h2>
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th class="table-purple" scope="col">Usuario</th>
                                        <th class="table-purple" scope="col">Correo</th>
                                        <th class="table-purple" scope="col">Tipo</th>
                                        <th class="table-purple" scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal de modificar usuario -->
            <div class="modal fade" id="modUserModal" tabindex="-1" aria-labelledby="modUserModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modUserModalLabel">Modificar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modUserForm">
                                <input type="hidden" id="editId" name="editId">
                                <div class="mb-3">
                                    <label for="editUserName" class="form-label">Nombre del Usuario</label>
                                    <input type="text" class="form-control" id="editUserName" name="editUserName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editRole" class="form-label">Tipo de usuario</label>
                                    <select class="form-select" id="editRole" name="editRole">
                                        <option value="admin">Administrador</option>
                                        <option value="ventas">Vendedor</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="editEmail" name="editEmail" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="modUserForm">Guardar Cambios</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>