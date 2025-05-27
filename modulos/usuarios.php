<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Ferretería</title>
    <link rel="stylesheet" href="../CSS/bootstrap.css">
    <script src="../JS/jquery-3.7.1.js"></script>
    <script src="../JS/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="../JS/usuarios/addUsuarios.js"></script>
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
                    <h2 class="text-primary"><i class="bi bi-person"></i> Usuarios</h2>
                    <button id="add_User" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus me-2"></i> Añadir usuario
                    </button>
                </div>
            </div>
            <!-- Base de datos a usar -->
            <?php 
            include_once "../components/changeDB.html"; 
            ?>
            <!-- Tabla de usuarios -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-primary"><i class="bi bi-person"></i> Lista de Usuarios</h2>
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col">Usuario</th>
                                        <th scope="col">Correo</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Contraseña</th>
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
            <!-- Modal de crear usuario -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Añadir Nuevo Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="userType" class="form-label">Tipo</label>
                                    <input type="text" class="form-control" id="userType" name="userType" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="text" class="form-control" id="password" name="password" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" form="addUserForm">Guardar Usuario</button>
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
                                    <label for="editUsername" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="editEmail" name="editEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editUserType" class="form-label">Tipo</label>
                                    <input type="text" class="form-control" id="editUserType" name="editUserType" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editPassword" class="form-label">Contraseña</label>
                                    <input type="text" class="form-control" id="editPassword" name="editPassword" required>
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
