<?php
header('Content-Type: application/json');
session_start();
require_once '../../connections.php';
require_once '../../vendor/autoload.php'; // Asegúrate de tenerlo si usas Composer

use MongoDB\BSON\ObjectId;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['_id']) || !isset($data['db_choice'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos insuficientes para eliminar usuario.']);
    exit;
}

$id = $data['_id'];
$dbChoice = $data['db_choice'] ?? 'local';

$conexion = $dbChoice === 'remote' ? $atlasConexion : $localConexion;
$origen = strtoupper($dbChoice);

if (!$conexion) {
    echo json_encode(['status' => 'error', 'message' => "No se pudo conectar a la base de datos $origen."]);
    exit;
}

try {
    $db = $conexion->selectDatabase('ferreteria');
    $collection = $db->selectCollection('users');
    $res = $collection->deleteOne(['_id' => new ObjectId($id)]);

    if ($res->getDeletedCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => "Usuario eliminado de $origen."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => "No se eliminó ningún usuario en $origen."]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => "Error al eliminar en $origen: " . $e->getMessage()]);
}
?>