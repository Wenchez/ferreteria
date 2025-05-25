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

    $productId     = $putVars['product_id']     ?? '';
    $productName   = $putVars['productName']    ?? '';
    $category      = $putVars['category']       ?? '';
    $supplierName  = $putVars['supplierName']   ?? '';
    $stock         = isset($putVars['stock'])   ? (int) $putVars['stock'] : null;
    $price         = isset($putVars['price'])   ? (float) $putVars['price'] : null;

    // Validar que haya ID
    if (empty($productId)) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el campo 'product_id'."]);
        exit;
    }
    // Construir el filtro y los datos a actualizar
    $filter = ['_id' => $productId];
    $updateFields = [];

    // Solo agregar al $set los campos que vengan no vacíos (para evitar sobreescribir con cadena vacía)
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

    // Bandera para saber si al menos una actualización tuvo éxito
    $updateSuccessful = false;

    // --- Actualizar en la base de datos LOCAL ---
    if ($localConexion) {
        try {
            $dbLocal        = $localConexion->selectDatabase('ferreteria');
            $collectionLoc  = $dbLocal->selectCollection('products');

            $resultLocal = $collectionLoc->updateOne($filter, $updateData);
            if ($resultLocal->getMatchedCount() === 0) {
                $respuestaLocal = "No se encontró producto con ID '$productId' en LOCAL.";
            }
            elseif ($resultLocal->getModifiedCount() === 0) {
                $respuestaLocal = "Producto con ID '$productId' en LOCAL existe, pero los datos ya estaban iguales.";
                $updateSuccessful = true;
            }
            else {
                $respuestaLocal = "Producto con ID '$productId' actualizado en LOCAL.";
                $updateSuccessful = true;
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $respuestaLocal = "Error al actualizar en LOCAL: " . $e->getMessage();
        }
    } else {
        $respuestaLocal = "Conexión LOCAL no disponible. No se pudo actualizar.";
    }

    // --- Actualizar en la base de datos REMOTA (ATLAS) ---
    if ($atlasConexion) {
        try {
            $dbAtlas         = $atlasConexion->selectDatabase('ferreteria');
            $collectionAtlas = $dbAtlas->selectCollection('products');

            $resultAtlas = $collectionAtlas->updateOne($filter, $updateData);
            if ($resultAtlas->getMatchedCount() === 0) {
                $respuestaAtlas = "No se encontró producto con ID '$productId' en REMOTO.";
            }
            elseif ($resultAtlas->getModifiedCount() === 0) {
                $respuestaAtlas = "Producto con ID '$productId' en REMOTO existe, pero los datos ya estaban iguales.";
                $updateSuccessful = true;
            }
            else {
                $respuestaAtlas = "Producto con ID '$productId' actualizado en REMOTO.";
                $updateSuccessful = true;
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $respuestaAtlas = "Error al actualizar en REMOTO: " . $e->getMessage();
        }
    } else {
        $respuestaAtlas = "Conexión REMOTA no disponible. No se pudo actualizar.";
    }

    // Devolver resultado según haya éxito o no
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
?>