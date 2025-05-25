<?php
    require __DIR__ . '/../../connections.php';

    header('Content-Type: application/json');

    // 1. Verificar que sea POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'status'         => 'error',
            'mensaje_local'  => 'Método no permitido. Use POST.',
            'mensaje_remoto' => 'Método no permitido. Use POST.'
        ]);
        exit;
    }

    // 2. Obtener y validar el ID
    $productId = $_POST['id'] ?? '';
    if (empty($productId)) {
        http_response_code(400);
        echo json_encode([
            'status'         => 'error',
            'mensaje_local'  => 'No se proporcionó el ID del producto.',
            'mensaje_remoto' => 'No se proporcionó el ID del producto.'
        ]);
        exit;
    }

    // Construir el filtro y los datos a actualizar
    $filter = ['_id' => $productId];

    $deleteLocalMsg  = '';
    $deleteRemoteMsg = '';
    $deleteSuccess   = false;

    // 3. Intentar eliminar en LOCAL
    if ($localConexion) {
        try {
            $dbLocal         = $localConexion->selectDatabase('ferreteria');
            $collectionLocal = $dbLocal->selectCollection('products');
            $resultLocal     = $collectionLocal->deleteOne($filter);

            if ($resultLocal->getDeletedCount() > 0) {
                $deleteLocalMsg = "Producto con ID '$productId' eliminado en LOCAL.";
                $deleteSuccess  = true;
            } else {
                $deleteLocalMsg = "Producto con ID '$productId' no encontrado en LOCAL.";
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $deleteLocalMsg = "Error al eliminar en LOCAL: " . $e->getMessage();
        }
    } else {
        $deleteLocalMsg = "Conexión LOCAL no disponible. No se pudo eliminar.";
    }

    // 4. Intentar eliminar en REMOTO
    if ($atlasConexion) {
        try {
            $dbAtlas          = $atlasConexion->selectDatabase('ferreteria');
            $collectionRemote = $dbAtlas->selectCollection('products');
            $resultRemote     = $collectionRemote->deleteOne($filter);

            if ($resultRemote->getDeletedCount() > 0) {
                $deleteRemoteMsg = "Producto con ID '$productId' eliminado en REMOTO.";
                $deleteSuccess   = true;
            } else {
                $deleteRemoteMsg = "Producto con ID '$productId' no encontrado en REMOTO.";
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            $deleteRemoteMsg = "Error al eliminar en REMOTO: " . $e->getMessage();
        }
    } else {
        $deleteRemoteMsg = "Conexión REMOTA no disponible. No se pudo eliminar.";
    }

    // 5. Responder según si al menos una eliminación fue exitosa
    if ($deleteSuccess) {
        http_response_code(200);
        echo json_encode([
            'status'         => 'success',
            'mensaje_local'  => $deleteLocalMsg,
            'mensaje_remoto' => $deleteRemoteMsg
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'status'         => 'error',
            'mensaje_local'  => $deleteLocalMsg,
            'mensaje_remoto' => $deleteRemoteMsg
        ]);
    }
?>