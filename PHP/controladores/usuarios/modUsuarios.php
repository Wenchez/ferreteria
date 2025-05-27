<?php
header('Content-Type: application/json');
session_start();
require_once '../../connections.php';
require_once '../../vendor/autoload.php'; // Asegúrate de incluir esto si no lo tenías

use MongoDB\BSON\ObjectId;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['_id']) || !isset($data['db_choice'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos insuficientes para modificar usuario.']);
    exit;
}

$id = $data['_id'];
$dbChoice = $data['db_choice'] ?? 'local';

$updateFields = [];
if (isset($data['username'])) $updateFields['username'] = $data['username'];
if (isset($data['email'])) $updateFields['email'] = $data['email'];
if (isset($data['userType'])) $updateFields['userType'] = $data['userType'];

// Elegir conexión
$conexion = $dbChoice === 'remote' ? $atlasConexion : $localConexion;
$origen = strtoupper($dbChoice);

if (!$conexion) {
    echo json_encode(['status' => 'error', 'message' => "No se pudo conectar a la base de datos $origen."]);
    exit;
}

try {
    $db = $conexion->selectDatabase('ferreteria');
    $collection = $db->selectCollection('users');
    $res = $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $updateFields]);

    if ($res->getModifiedCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => "Usuario modificado en $origen."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "No se modificó ningún dato en $origen."]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Error al modificar en $origen: " . $e->getMessage()]);
}
