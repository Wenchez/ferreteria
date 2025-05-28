<?php
require __DIR__ . '/../../connections.php';

header('Content-Type: application/json');

// 1. Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Método no permitido. Use POST.'
    ]);
    exit;
}

// 2. Obtener elección de base de datos (POST o GET)
$dbChoice = $_GET['db_choice'] ?? $_POST['db_choice'] ?? 'local';
if ($dbChoice === 'remote') {
    $dbConnection   = $atlasConexion;
    $connectionType = 'REMOTE';
} else {
    $dbConnection   = $localConexion;
    $connectionType = 'LOCAL';
}

if (!$dbConnection) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => "No se pudo establecer conexión con la base de datos seleccionada ($connectionType)."
    ]);
    exit;
}

// 3. Obtener y validar el ID de la venta
$saleId = $_POST['id'] ?? '';
if (empty($saleId)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'No se proporcionó el ID de la venta.'
    ]);
    exit;
}

try {
    $db         = $dbConnection->selectDatabase('ferreteria');
    $collection = $db->selectCollection('sales');
    $filter     = ['_id' => $saleId];

    $result = $collection->deleteOne($filter);
    if ($result->getDeletedCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => "Venta con ID '$saleId' no encontrada en $connectionType.",
            'db'      => $connectionType
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            'status'  => 'success',
            'message' => "Venta con ID '$saleId' eliminada en $connectionType.",
            'db'      => $connectionType
        ]);
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => "Error al eliminar venta en $connectionType: " . $e->getMessage()
    ]);
}
?>