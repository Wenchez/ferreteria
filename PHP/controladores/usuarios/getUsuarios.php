<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$action = $_GET['action'] ?? '';
$db_choice = $_GET['db_choice'] ?? 'local';

// Selección de conexión
$dbConnection = null;
if ($db_choice === 'remote') {
    $dbConnection = $atlasConexion;
    $connectionType = 'REMOTA';
} else {
    $dbConnection = $localConexion;
    $connectionType = 'LOCAL';
}

if (!$dbConnection) {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo establecer conexión con la base de datos seleccionada.']);
    exit;
}

$db = $dbConnection->selectDatabase('ferreteria');
$collection = $db->selectCollection('users');

if ($action === 'getUsers') {
    $usuarios = [];
    try {
        $cursor = $collection->find();
        foreach ($cursor as $doc) {
            $usuarios[] = [
                '_id' => (string)$doc['_id'],
                'username' => $doc['username'] ?? '',
                'email' => $doc['email'] ?? '',
                'userType' => $doc['userType'] ?? ''
            ];
        }
        echo json_encode([
            'status' => 'success',
            'usuarios' => $usuarios,
            'db' => $connectionType
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener usuarios de ' . $connectionType . ': ' . $e->getMessage()
        ]);
    }
    exit;
}
if ($action === 'getUserById' && isset($_GET['userId'])) {
    $usuario = [];
    try {
        $id = $_GET['userId'];
        $doc = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if ($doc) {
            $usuario = [
                '_id' => (string)$doc['_id'],
                'username' => $doc['username'] ?? '',
                'email' => $doc['email'] ?? '',
                'userType' => $doc['userType'] ?? ''
            ];
        }
        echo json_encode([
            'status' => 'success',
            'usuario' => $usuario,
            'db' => $connectionType
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al obtener usuarios de ' . $connectionType . ': ' . $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
?>