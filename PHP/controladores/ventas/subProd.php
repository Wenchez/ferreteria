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

$productId          = trim($putVars['product_id'] ?? '');
$quantityActualized = isset($putVars['quantityActualized']) ? (int)$putVars['quantityActualized'] : null;

// 3) Validar que se envíe product_id y quantityActualized
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

// 4) Construir filtro y datos a actualizar (solo stock cambia)
$filter = ['_id' => $productId];
$updateFields = ['stock' => $quantityActualized];
$updateData   = ['$set' => $updateFields];

$updateSuccessful = false;
$respuestaLocal   = '';
$respuestaAtlas   = '';

// --- Actualizar en la base de datos LOCAL ---
if ($localConexion) {
    try {
        $dbLocal       = $localConexion->selectDatabase('ferreteria');
        $collectionLoc = $dbLocal->selectCollection('products');

        $resultLocal = $collectionLoc->updateOne($filter, $updateData);

        if ($resultLocal->getMatchedCount() === 0) {
            $respuestaLocal = "No se encontró producto con ID '$productId' en LOCAL.";
        }
        elseif ($resultLocal->getModifiedCount() === 0) {
            $respuestaLocal = "Producto con ID '$productId' en LOCAL existe, pero el stock ya era igual a $quantityActualized.";
            $updateSuccessful = true;
        }
        else {
            $respuestaLocal   = "Stock del producto con ID '$productId' actualizado a $quantityActualized en LOCAL.";
            $updateSuccessful = true;
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $respuestaLocal = "Error al actualizar stock en LOCAL: " . $e->getMessage();
    }
} else {
    $respuestaLocal = "Conexión LOCAL no disponible. No se pudo actualizar stock.";
}

// --- Actualizar en la base de datos REMOTA (ATLAS) ---
if ($atlasConexion) {
    try {
        $dbAtlas        = $atlasConexion->selectDatabase('ferreteria');
        $collectionAtl  = $dbAtlas->selectCollection('products');

        $resultAtlas = $collectionAtl->updateOne($filter, $updateData);

        if ($resultAtlas->getMatchedCount() === 0) {
            $respuestaAtlas = "No se encontró producto con ID '$productId' en REMOTO.";
        }
        elseif ($resultAtlas->getModifiedCount() === 0) {
            $respuestaAtlas = "Producto con ID '$productId' en REMOTO existe, pero el stock ya era igual a $quantityActualized.";
            $updateSuccessful = true;
        }
        else {
            $respuestaAtlas  = "Stock del producto con ID '$productId' actualizado a $quantityActualized en REMOTO.";
            $updateSuccessful = true;
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $respuestaAtlas = "Error al actualizar stock en REMOTO: " . $e->getMessage();
    }
} else {
    $respuestaAtlas = "Conexión REMOTA no disponible. No se pudo actualizar stock.";
}

// 5) Devolver resultado según haya éxito o no
if ($updateSuccessful) {
    http_response_code(200);
    echo json_encode([
        "status"         => "success",
        "mensaje_local"  => $respuestaLocal,
        "mensaje_remoto" => $respuestaAtlas
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        "status"         => "error",
        "mensaje_local"  => $respuestaLocal,
        "mensaje_remoto" => $respuestaAtlas
    ]);
}