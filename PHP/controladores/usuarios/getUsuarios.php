<?php
header('Content-Type: application/json');
require_once '../../connections.php';

$data = json_decode(file_get_contents('php://input'), true);
$db_choice = $data['db_choice'] ?? 'local';
$result = [];
$error = '';

if ($db_choice === 'both') {
    // Unificar usuarios de ambas bases
    if ($localConexion) {
        try {
            $dbLocal = $localConexion->selectDatabase('ferreteria');
            $collection = $dbLocal->selectCollection('users');
            $usuarios = $collection->find();
            foreach ($usuarios as $usuario) {
                $usuario['_id'] = (string)$usuario['_id'];
                $usuario['db_origin'] = 'LOCAL';
                $result[] = $usuario;
            }
        } catch (Exception $e) {
            $error .= 'Error al obtener usuarios de LOCAL: ' . $e->getMessage() . ' ';
        }
    }
    if ($atlasConexion) {
        try {
            $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
            $collection = $dbAtlas->selectCollection('users');
            $usuarios = $collection->find();
            foreach ($usuarios as $usuario) {
                $usuario['_id'] = (string)$usuario['_id'];
                $usuario['db_origin'] = 'REMOTA';
                $result[] = $usuario;
            }
        } catch (Exception $e) {
            $error .= 'Error al obtener usuarios de REMOTO: ' . $e->getMessage();
        }
    }
    if (!$localConexion && !$atlasConexion) {
        $error = 'No hay conexiones disponibles.';
    }
} else if ($db_choice === 'local') {
    if ($localConexion) {
        try {
            $dbLocal = $localConexion->selectDatabase('ferreteria');
            $collection = $dbLocal->selectCollection('users');
            $usuarios = $collection->find();
            foreach ($usuarios as $usuario) {
                $usuario['_id'] = (string)$usuario['_id'];
                $usuario['db_origin'] = 'LOCAL';
                $result[] = $usuario;
            }
        } catch (Exception $e) {
            $error = 'Error al obtener usuarios de LOCAL: ' . $e->getMessage();
        }
    } else {
        $error = 'Conexión LOCAL no disponible.';
    }
} else if ($db_choice === 'remote') {
    if ($atlasConexion) {
        try {
            $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
            $collection = $dbAtlas->selectCollection('users');
            $usuarios = $collection->find();
            foreach ($usuarios as $usuario) {
                $usuario['_id'] = (string)$usuario['_id'];
                $usuario['db_origin'] = 'REMOTA';
                $result[] = $usuario;
            }
        } catch (Exception $e) {
            $error = 'Error al obtener usuarios de REMOTO: ' . $e->getMessage();
        }
    } else {
        $error = 'Conexión REMOTA no disponible.';
    }
} else {
    $error = 'db_choice inválido.';
}

if ($error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $error]);
} else {
    echo json_encode(['status' => 'success', 'usuarios' => $result]);
}
