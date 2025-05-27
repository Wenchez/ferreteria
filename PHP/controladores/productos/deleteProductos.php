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

    // 2. Obtener elección de base de datos (GET o POST)
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

    // 3. Obtener y validar el ID del producto
    $productId = $_POST['id'] ?? '';
    if (empty($productId)) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => "No se proporcionó el ID del producto."
        ]);
        exit;
    }

    try {
        $db         = $dbConnection->selectDatabase('ferreteria');
        $collection = $db->selectCollection('products');
        $filter     = ['_id' => $productId];

        $result = $collection->deleteOne($filter);
        if ($result->getDeletedCount() === 0) {
            // No existía el documento
            http_response_code(404);
            echo json_encode([
                'status'  => 'error',
                'message' => "Producto con ID '$productId' no encontrado en $connectionType.",
                'db'      => $connectionType
            ]);
        } else {
            // Eliminado correctamente
            http_response_code(200);
            echo json_encode([
                'status'  => 'success',
                'message' => "Producto con ID '$productId' eliminado en $connectionType.",
                'db'      => $connectionType
            ]);
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status'  => 'error',
            'message' => "Error al eliminar en $connectionType: " . $e->getMessage()
        ]);
    }
?>