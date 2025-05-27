<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'No se recibieron datos']);
    exit;
}

$insertLocalMsg = '';
$insertRemoteMsg = '';
$insertSuccess = false;
$insertedId = null;

// Insertar en LOCAL
if ($localConexion) {
    try {
        $dbLocal = $localConexion->selectDatabase('ferreteria');
        $collectionLocal = $dbLocal->selectCollection('users');
        $resLocal = $collectionLocal->insertOne([
            'username' => $data['username'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $data['password'] ?? '',
            'role' => $data['role'] ?? ''
        ]);
        $insertLocalMsg = 'Usuario insertado en LOCAL (ID: ' . $resLocal->getInsertedId() . ')';
        $insertedId = (string)$resLocal->getInsertedId();
        $insertSuccess = true;
    } catch (Exception $e) {
        $insertLocalMsg = 'Error al insertar en LOCAL: ' . $e->getMessage();
    }
} else {
    $insertLocalMsg = 'Conexión LOCAL no disponible. No se pudo insertar.';
}

// Insertar en REMOTO
if ($atlasConexion) {
    try {
        $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
        $collectionRemote = $dbAtlas->selectCollection('users');
        $resRemote = $collectionRemote->insertOne([
            'username' => $data['username'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $data['password'] ?? '',
            'role' => $data['role'] ?? ''
        ]);
        $insertRemoteMsg = 'Usuario insertado en REMOTO (ID: ' . $resRemote->getInsertedId() . ')';
        $insertSuccess = true;
    } catch (Exception $e) {
        $insertRemoteMsg = 'Error al insertar en REMOTO: ' . $e->getMessage();
    }
} else {
    $insertRemoteMsg = 'Conexión REMOTA no disponible. No se pudo insertar.';
}

if ($insertSuccess) {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'inserted_id' => $insertedId,
        'message_local' => $insertLocalMsg,
        'message_remote' => $insertRemoteMsg
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message_local' => $insertLocalMsg,
        'message_remote' => $insertRemoteMsg
    ]);
}
