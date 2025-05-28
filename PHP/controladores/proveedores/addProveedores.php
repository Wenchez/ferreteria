<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);
$dbChoice = $_GET['db_choice'] ?? $_POST['db_choice'] ?? $data['db_choice'] ?? 'local';

if (!$data) {
    echo json_encode(['error' => 'No se recibieron datos']);
    exit;
}

// Selección de conexión
$dbConnection = null;
if ($dbChoice === 'remote') {
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

try {
    $db = $dbConnection->selectDatabase('ferreteria');
    $collection = $db->selectCollection('suppliers');
    $insertResult = $collection->insertOne([
        'supplierName' => $data['supplierName'] ?? '',
        'contactName' => $data['contactName'] ?? '',
        'phone' => $data['phone'] ?? '',
        'email' => $data['email'] ?? '',
        'address' => $data['address'] ?? ''
    ]);
    echo json_encode([
        'success' => true,
        'insertedId' => (string)$insertResult->getInsertedId(),
        'db' => $connectionType
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>