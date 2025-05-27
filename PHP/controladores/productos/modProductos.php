<?php
    require __DIR__ . '/../../connections.php';

    header('Content-Type: application/json');

    // Asegurarse de que la petición sea PUT
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(["error" => "Acceso inválido. Este endpoint solo acepta peticiones PUT."]);
        exit;
    }

    // PHP no llena $_PUT automáticamente, por lo que hay que parsear php://input
    parse_str(file_get_contents("php://input"), $putVars);

    // Leer elección de base de datos (GET o en body)
    $dbChoice    = $_GET['db_choice'] ?? $putVars['db_choice'] ?? 'local';
    if ($dbChoice === 'remote') {
        $dbConnection   = $atlasConexion;
        $connectionType = 'REMOTE';
    } else {
        $dbConnection   = $localConexion;
        $connectionType = 'LOCAL';
    }

    if (!$dbConnection) {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo establecer conexión con la base de datos seleccionada."]);
        exit;
    }

    $productId    = $putVars['product_id']     ?? '';
    $productName  = $putVars['productName']    ?? '';
    $category     = $putVars['category']       ?? '';
    $supplierName = $putVars['supplierName']   ?? '';
    $stock        = isset($putVars['stock'])   ? (int)   $putVars['stock'] : null;
    $price        = isset($putVars['price'])   ? (float) $putVars['price'] : null;

    // Validar que haya ID
    if (empty($productId)) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el campo 'product_id'."]);
        exit;
    }

    // Construir el filtro y los datos a actualizar
    $filter       = ['_id' => $productId];
    $updateFields = [];
    if ($productName !== '')  { $updateFields['productName']  = $productName; }
    if ($category !== '')     { $updateFields['category']     = $category; }
    if ($supplierName !== '') { $updateFields['supplierName'] = $supplierName; }
    if ($stock !== null)      { $updateFields['stock']        = $stock; }
    if ($price !== null)      { $updateFields['price']        = $price; }

    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(["error" => "No se recibieron campos válidos para actualizar."]);
        exit;
    }

    $updateData = ['$set' => $updateFields];

    try {
        $db         = $dbConnection->selectDatabase('ferreteria');
        $collection = $db->selectCollection('products');

        $result = $collection->updateOne($filter, $updateData);
        if ($result->getMatchedCount() === 0) {
            http_response_code(404);
            echo json_encode([
                "status"  => "error",
                "message" => "No se encontró producto con ID '$productId' en $connectionType."
            ]);
            exit;
        }

        if ($result->getModifiedCount() === 0) {
            // Existe pero sin cambios
            http_response_code(200);
            echo json_encode([
                "status"  => "success",
                "message" => "Producto con ID '$productId' en $connectionType existe, pero los datos ya estaban iguales."
            ]);
        } else {
            // Actualizado correctamente
            http_response_code(200);
            echo json_encode([
                "status"  => "success",
                "message" => "Producto con ID '$productId' actualizado en $connectionType."
            ]);
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Error al actualizar en $connectionType: " . $e->getMessage()
        ]);
    }
?>