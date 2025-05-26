<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.html");
    exit();
}

// Conexión MongoDB (elige local o atlas según GET o SESSION)
require_once "../PHP/connections.php"; // Debe definir $localDb y $atlasDb

// Selección de base de datos (por GET, SESSION o default local)
if (isset($_GET['db'])) {
    $_SESSION['mongo_db'] = $_GET['db'];
}
$dbType = $_SESSION['mongo_db'] ?? 'local';

if ($dbType === 'atlas') {
    $collection = $atlasDb->suppliers;
} else {
    $collection = $localDb->suppliers;
}

// Crear o actualizar proveedor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? new MongoDB\BSON\ObjectId($_POST['id']) : null;
    $data = [
        'supplierName' => $_POST['supplierName'],
        'address'      => $_POST['address'],
        'phone'        => $_POST['phone'],
        'email'        => $_POST['email']
    ];
    if ($id) {
        $collection->updateOne(['_id' => $id], ['$set' => $data]);
    } else {
        $collection->insertOne($data);
    }
    header("Location: proveedores.php");
    exit();
}

// Eliminar proveedor
if (isset($_GET['delete'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['delete']);
    $collection->deleteOne(['_id' => $id]);
    header("Location: proveedores.php");
    exit();
}

// Editar proveedor
$editProveedor = null;
if (isset($_GET['edit'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['edit']);
    $editProveedor = $collection->findOne(['_id' => $id]);
}

// Listar proveedores
$proveedores = $collection->find([], ['sort' => ['_id' => -1]]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores - Ferretería</title>
    <link rel="stylesheet" href="../CSS/bootstrap.css">
    <script src="../JS/jquery-3.7.1.js"></script>
    <script src="../JS/bootstrap.bundle.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex">
    <!-- Sidebar -->
    <?php 
    $activePage = 'proveedores';
    include_once "../components/sidebar.php"; 
    ?>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0 text-warning"><i class="bi bi-truck"></i> Proveedores</h2>
                <div>
                    <a href="?db=local" class="btn btn-outline-secondary btn-sm <?= $dbType === 'local' ? 'active' : '' ?>">Local</a>
                    <a href="?db=atlas" class="btn btn-outline-secondary btn-sm <?= $dbType === 'atlas' ? 'active' : '' ?>">Atlas</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalProveedor" onclick="limpiarModal()">
                        <i class="bi bi-plus-circle"></i> Agregar proveedor
                    </button>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                foreach ($proveedores as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['supplierName']) ?></td>
                                        <td><?= htmlspecialchars($row['address']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td>
                                            <a href="proveedores.php?edit=<?= $row['_id'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil"></i></a>
                                            <a href="proveedores.php?delete=<?= $row['_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar proveedor?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if ($i === 1): ?>
                                    <tr><td colspan="6" class="text-center">No hay proveedores registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar/Editar Proveedor -->
            <div class="modal fade <?= $editProveedor ? 'show' : '' ?>" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedorLabel" aria-hidden="true" style="<?= $editProveedor ? 'display:block;' : '' ?>">
              <div class="modal-dialog">
                <form method="post" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalProveedorLabel"><?= $editProveedor ? 'Editar proveedor' : 'Agregar proveedor' ?></h5>
                    <a href="proveedores.php" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></a>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" name="id" value="<?= $editProveedor ? $editProveedor['_id'] : '' ?>">
                      <div class="mb-3">
                          <label for="nombreProveedor" class="form-label">Nombre</label>
                          <input type="text" class="form-control" id="nombreProveedor" name="supplierName" required value="<?= $editProveedor ? htmlspecialchars($editProveedor['supplierName']) : '' ?>">
                      </div>
                      <div class="mb-3">
                          <label for="direccionProveedor" class="form-label">Dirección</label>
                          <input type="text" class="form-control" id="direccionProveedor" name="address" required value="<?= $editProveedor ? htmlspecialchars($editProveedor['address']) : '' ?>">
                      </div>
                      <div class="mb-3">
                          <label for="telefonoProveedor" class="form-label">Teléfono</label>
                          <input type="text" class="form-control" id="telefonoProveedor" name="phone" required value="<?= $editProveedor ? htmlspecialchars($editProveedor['phone']) : '' ?>">
                      </div>
                      <div class="mb-3">
                          <label for="emailProveedor" class="form-label">Email</label>
                          <input type="email" class="form-control" id="emailProveedor" name="email" required value="<?= $editProveedor ? htmlspecialchars($editProveedor['email']) : '' ?>">
                      </div>
                  </div>
                  <div class="modal-footer">
                    <a href="proveedores.php" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                  </div>
                </form>
              </div>
            </div>
            <?php if ($editProveedor): ?>
            <script>
                $(document).ready(function(){
                    var modal = new bootstrap.Modal(document.getElementById('modalProveedor'));
                    modal.show();
                });
            </script>
            <?php endif; ?>
        </div>
    </div>
    <script>
    function limpiarModal() {
        $('#modalProveedor input').val('');
    }
    </script>
</body>
</html>