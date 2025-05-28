<?php
require __DIR__ . '/../../connections.php';

header('Content-Type: application/json');

// 1) Asegurarse de que la petición sea PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["error" => "Acceso inválido. Este endpoint solo acepta peticiones PUT."]);
    exit;
}

// 2) PHP no llena $_PUT automáticamente, así que parseamos php://input
parse_str(file_get_contents("php://input"), $putVars);

// 3) Leer elección de base de datos (GET o en body)
$dbChoice = $_GET['db_choice'] ?? $putVars['db_choice'] ?? 'local';
if ($dbChoice === 'remote') {
    $dbConnection   = $atlasConexion;
    $connectionType = 'REMOTE';
} else {
    $dbConnection   = $localConexion;
    $connectionType = 'LOCAL';
}

if (!$dbConnection) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo establecer conexión con la base de datos seleccionada ($connectionType)."]);
    exit;
}

$productId          = trim($putVars['product_id'] ?? '');
$quantityActualized = isset($putVars['quantityActualized']) ? (int)$putVars['quantityActualized'] : null;

// 4) Validar que se envíe product_id y quantityActualized
if (empty($productId)) {
    http_response_code(400);
    echo json_encode(["error" => "Falta el campo 'product_id'."]);
    exit;
}

if ($quantityActualized === null || $quantityActualized < 0) {
    http_response_code(400);
    echo json_encode(["error" => "Falta o es inválido el campo 'quantityActualized'."]);
    exit;
}

// 5) Construir filtro y datos a actualizar (solo stock cambia)
$filter       = ['_id' => $productId];
$updateData   = ['$set' => ['stock' => $quantityActualized]];

try {
    $db         = $dbConnection->selectDatabase('ferreteria');
    $collection = $db->selectCollection('products');
    $result     = $collection->updateOne($filter, $updateData);

    if ($result->getMatchedCount() === 0) {
        http_response_code(404);
        echo json_encode([
            "status"  => "error",
            "message" => "No se encontró producto con ID '$productId' en $connectionType."
        ]);
        exit;
    }

    if ($result->getModifiedCount() === 0) {
        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Producto con ID '$productId' en $connectionType existe, pero el stock ya era igual a $quantityActualized."
        ]);
    } else {
        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Stock del producto con ID '$productId' actualizado a $quantityActualized en $connectionType."
        ]);
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Error al actualizar stock en $connectionType: " . $e->getMessage()
    ]);
}
?>