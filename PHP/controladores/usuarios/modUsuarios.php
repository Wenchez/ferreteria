<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos insuficientes para modificar usuario.']);
    exit;
}

$id = $data['_id'];
$updateFields = [];
if (isset($data['username'])) $updateFields['username'] = $data['username'];
if (isset($data['email'])) $updateFields['email'] = $data['email'];
if (isset($data['password'])) $updateFields['password'] = $data['password'];
if (isset($data['role'])) $updateFields['role'] = $data['role'];

use MongoDB\BSON\ObjectId;

$updateLocalMsg = '';
$updateRemoteMsg = '';
$updateSuccess = false;

// Modificar en LOCAL
if ($localConexion) {
    try {
        $dbLocal = $localConexion->selectDatabase('ferreteria');
        $collectionLocal = $dbLocal->selectCollection('users');
        $resLocal = $collectionLocal->updateOne([
            '_id' => new ObjectId($id)
        ], ['$set' => $updateFields]);
        $updateLocalMsg = 'Usuario modificado en LOCAL.';
        $updateSuccess = $resLocal->getModifiedCount() > 0;
    } catch (Exception $e) {
        $updateLocalMsg = 'Error al modificar en LOCAL: ' . $e->getMessage();
    }
} else {
    $updateLocalMsg = 'Conexión LOCAL no disponible. No se pudo modificar.';
}

// Modificar en REMOTO
if ($atlasConexion) {
    try {
        $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
        $collectionRemote = $dbAtlas->selectCollection('users');
        $resRemote = $collectionRemote->updateOne([
            '_id' => new ObjectId($id)
        ], ['$set' => $updateFields]);
        $updateRemoteMsg = 'Usuario modificado en REMOTO.';
        $updateSuccess = $updateSuccess || $resRemote->getModifiedCount() > 0;
    } catch (Exception $e) {
        $updateRemoteMsg = 'Error al modificar en REMOTO: ' . $e->getMessage();
    }
} else {
    $updateRemoteMsg = 'Conexión REMOTA no disponible. No se pudo modificar.';
}

if ($updateSuccess) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message_local' => $updateLocalMsg,
        'message_remote' => $updateRemoteMsg
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message_local' => $updateLocalMsg,
        'message_remote' => $updateRemoteMsg
    ]);
}
