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
    $collection = $db->selectCollection('clients');

    if ($action === 'getClients') {
        $clientes = [];
        $cursor = $collection->find();
        foreach ($cursor as $doc) {
            $clientes[] = [
                '_id' => (string)$doc['_id'],
                'clientName' => $doc['clientName'] ?? '',
                'phone' => $doc['phone'] ?? '',
                'email' => $doc['email'] ?? '',
                'address' => $doc['address'] ?? ''
            ];
        }
        echo json_encode([
            'clientes' => $clientes,
            'db' => $connectionType
        ]);
        exit;
    }

    if ($action === 'getClientById' && isset($_GET['clientId'])) {
        $id = $_GET['clientId'];
        $doc = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if ($doc) {
            $cliente = [
                '_id' => (string)$doc['_id'],
                'clientName' => $doc['clientName'] ?? '',
                'phone' => $doc['phone'] ?? '',
                'email' => $doc['email'] ?? '',
                'address' => $doc['address'] ?? ''
            ];
            echo json_encode([
                'cliente' => $cliente,
                'db' => $connectionType
            ]);
        } else {
            echo json_encode(['error' => 'Cliente no encontrado']);
        }
        exit;
    }

    echo json_encode(['error' => 'Acción no válida']);
?>