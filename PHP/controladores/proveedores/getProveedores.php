<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$action = $_GET['action'] ?? '';
$db_choice = $_GET['db_choice'] ?? 'local';

// Selección de conexión
$dbConnection = null;
if ($db_choice === 'remote') {
    $dbConnection = $atlasConexion;
    $connectionType = 'REMOTE';
} else {
    $dbConnection = $localConexion;
    $connectionType = 'LOCAL';
}

if (!$dbConnection) {
    echo json_encode(['error' => 'No se pudo establecer conexión con la base de datos seleccionada.']);
    exit;
}

$db = $dbConnection->selectDatabase('ferreteria');
$collection = $db->selectCollection('suppliers');

if ($action === 'getSuppliers') {
    $proveedores = [];
    $cursor = $collection->find();
    foreach ($cursor as $doc) {
        $proveedores[] = [
            '_id' => (string)$doc['_id'],
            'supplierName' => $doc['supplierName'] ?? '',
            'contactName' => $doc['contactName'] ?? '',
            'phone' => $doc['phone'] ?? '',
            'email' => $doc['email'] ?? '',
            'address' => $doc['address'] ?? ''
        ];
    }
    echo json_encode([
        'proveedores' => $proveedores,
        'db' => $connectionType
    ]);
    exit;
}

if ($action === 'getSupplierById' && isset($_GET['supplierId'])) {
    $id = $_GET['supplierId'];
    $doc = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    if ($doc) {
        $proveedor = [
            '_id' => (string)$doc['_id'],
            'supplierName' => $doc['supplierName'] ?? '',
            'contactName' => $doc['contactName'] ?? '',
            'phone' => $doc['phone'] ?? '',
            'email' => $doc['email'] ?? '',
            'address' => $doc['address'] ?? ''
        ];
        echo json_encode([
            'proveedor' => $proveedor,
            'db' => $connectionType
        ]);
    } else {
        echo json_encode(['error' => 'Proveedor no encontrado']);
    }
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
