<?php

require 'connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $userType = $_POST['rol'] ?? '';

    $newUserData = [
        'username' => $username,
        'password' => $password,
        'userType' => $userType,
        'email' => $email
    ];

	$insertionSuccessful = false;

    // --- Inserción en la base de datos LOCAL ---
    if ($localConexion) {
        try {
            $dbLocal = $localConexion->selectDatabase('ferreteria');
            $collectionLocal = $dbLocal->selectCollection('users');

            $resultLocal = $collectionLocal->insertOne($newUserData);
            $insertedIdLocal = $resultLocal->getInsertedId();
			$insertionSuccessful = true;

        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Error al guardar en la base de datos LOCAL: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "La conexión LOCAL no está disponible. No se pudo guardar el usuario localmente.<br>";
    }

    //echo "<br>";

    // --- Inserción en la base de datos de ATLAS ---
    if ($atlasConexion) {
        try {
            $dbAtlas = $atlasConexion->selectDatabase('ferreteria');
            $collectionAtlas = $dbAtlas->selectCollection('users');

            $resultAtlas = $collectionAtlas->insertOne($newUserData);
            $insertedIdAtlas = $resultAtlas->getInsertedId();
			$insertionSuccessful = true;

        } catch (MongoDB\Driver\Exception\Exception $e) {
            echo "Error al guardar en la base de datos ATLAS: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "La conexión ATLAS no está disponible. No se pudo guardar el usuario remotamente.<br>";
    }

	if ($insertionSuccessful) {
        header("Location: ../login.php"); // Redirige al usuario a login.php
        exit(); // Es crucial llamar a exit() después de header()
    } else {
        // En caso de que ninguna inserción fue exitosa o ambas conexiones fallaron
        echo "Ocurrió un error al guardar el usuario. Por favor, inténtalo de nuevo más tarde.";
        // Puedes agregar un botón para regresar o un enlace.
    }

} else {
    echo "Acceso inválido. Este script solo acepta peticiones POST.";
}

?>