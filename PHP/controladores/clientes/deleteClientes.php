<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);
$dbChoice = $_GET['db_choice'] ?? $_POST['db_choice'] ?? $data['db_choice'] ?? 'local';

if (!$data || !isset($data['_id'])) {
    echo json_encode(['error' => 'ID no recibido']);
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
    $collection = $db->selectCollection('clients');
    $deleteResult = $collection->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($data['_id'])
    ]);
    echo json_encode([
        'success' => true,
        'deletedCount' => $deleteResult->getDeletedCount(),
        'db' => $connectionType
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
