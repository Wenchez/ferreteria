<?php

require 'connections.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $dbChoice = $_POST['db_choice'] ?? 'local'; // Valor por defecto 'local' si no se envía

    $dbConnection = null;
    $connectionType = '';
    $message = '';
    $loggedIn = false;
    $userFound = null;

    // --- Determinar qué conexión usar ---
    if ($dbChoice === 'atlas') {
        $dbConnection = $atlasConexion;
        $connectionType = 'ATLAS';
    } else { // Por defecto o si es 'local'
        $dbConnection = $localConexion;
        $connectionType = 'LOCAL';
    }

    if ($dbConnection) {
        try {
            $db = $dbConnection->selectDatabase('ferreteria');
            $collection = $db->selectCollection('users');

            // Buscar el usuario con las credenciales dadas
            $userFound = $collection->findOne([
                'username' => $username,
                'password' => $password
            ]);

            if ($userFound) {
                $loggedIn = true;
                $message = "Bienvenido, {$userFound['username']} (Conexión {$connectionType}).";
                session_start();
                $_SESSION['UserID'] = (string)$userFound['_id'];
                $_SESSION['Username'] = $userFound['username'];
                $_SESSION['UserType'] = $userFound['userType'];
            } else {
                $message = "Usuario y/o contraseña incorrectos en la base de datos {$connectionType}.";
            }

        } catch (MongoDB\Driver\Exception\Exception $e) {
            error_log("Error al consultar en BD {$connectionType}: " . $e->getMessage());
            $message = "Error de conexión con la base de datos {$connectionType}.";
        }
    } else {
        $message = "La conexión {$connectionType} no está disponible.";
    }

    // --- Enviar respuesta JSON al cliente ---
    if ($loggedIn) {
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'user' => [
                'username' => $userFound['username'],
                'userType' => $userFound['userType'],
                'email' => $userFound['email'] ?? null
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
    }

    exit();

} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Acceso inválido. Este script solo acepta peticiones POST.'
    ]);
    exit();
}

?>