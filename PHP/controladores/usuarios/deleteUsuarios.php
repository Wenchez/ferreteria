<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de usuario no proporcionado.']);
    exit;
}

$id = $data['_id'];
use MongoDB\BSON\ObjectId;

$deleteLocalMsg = '';
$deleteRemoteMsg = '';
$deleteSuccess = false;

// Eliminar en LOCAL
if ($localConexion) {
    try {
        $dbLocal = $localConexion->selectDatabase('ferreteria');
        $collectionLocal = $dbLocal->selectCollection('users');
        $resLocal = $collectionLocal->deleteOne(['_id' => new ObjectId($id)]);
        $deleteLocalMsg = 'Usuario eliminado en LOCAL.';
        $deleteSuccess = $resLocal->getDeletedCount() > 0;
    } catch (Exception $e) {
        $deleteLocalMsg = 'Error al eliminar en LOCAL: ' . $e->getMessage();
    }
} else {
    $deleteLocalMsg = 'Conexión LOCAL no disponible. No se pudo eliminar.';
}

// Eliminar en REMOTO
if ($atlasConexion) {
    try {
        $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
        $collectionRemote = $dbAtlas->selectCollection('users');
        $resRemote = $collectionRemote->deleteOne(['_id' => new ObjectId($id)]);
        $deleteRemoteMsg = 'Usuario eliminado en REMOTO.';
        $deleteSuccess = $deleteSuccess || $resRemote->getDeletedCount() > 0;
    } catch (Exception $e) {
        $deleteRemoteMsg = 'Error al eliminar en REMOTO: ' . $e->getMessage();
    }
} else {
    $deleteRemoteMsg = 'Conexión REMOTA no disponible. No se pudo eliminar.';
}

if ($deleteSuccess) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message_local' => $deleteLocalMsg,
        'message_remote' => $deleteRemoteMsg
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message_local' => $deleteLocalMsg,
        'message_remote' => $deleteRemoteMsg
    ]);
}
