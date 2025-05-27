<?php

    require __DIR__ . '/../../connections.php';

    header('Content-Type: application/json');

    // --- Determinar qué conexión usar ---
    $dbChoice = $_GET['db_choice'] ?? 'local'; // Valor por defecto 'local' si no se envía

    $dbConnection = null;
    $connectionType = '';

    if ($dbChoice === 'remote') {
        $dbConnection = $atlasConexion;
        $connectionType = 'REMOTE';
    } else {
        $dbConnection = $localConexion;
        $connectionType = 'LOCAL';
    }

    // Leer la acción solicitada
    $action = $_GET['action'] ?? '';

    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(405);
        echo json_encode(["error" => "Solo se permiten solicitudes GET"]);
        exit;
    }

    if (!$dbConnection) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "No se pudo establecer conexión con la base de datos seleccionada."]);
        exit; // Terminar ejecución si no hay conexión
    }

    try {
        $db = $dbConnection->selectDatabase('ferreteria');
        $coleccion = $db->selectCollection('sales');

        if ($action === "getSales") {
            // Caso 1: devolver todos los productos
            try{
                $documentos = $coleccion->find();

                $ventas = [];
                foreach ($documentos as $venta) {
                    $ventas[] = $venta;
                }
                echo json_encode([
                    'ventas'=>$ventas,
                    'db'=>$connectionType
                ]);
            }
            catch (Exception $e){
                echo json_encode("Error durante la operación: " . $e->getMessage());
            }
            exit;
        }
        elseif ($action === "getSaleById") {
            // Caso 2: devolver un solo producto según su ID
            $ventaId = $_GET['ventaId'] ?? '';
            if (empty($ventaId)) {
                http_response_code(400);
                echo json_encode(["error" => "No se proporcionó productId"]);
                exit;
            }

            // Construir el filtro y los datos a actualizar
            $filtro = ['_id' => $ventaId];
            $venta = $coleccion->findOne($filtro);

            if (!$venta) {
                http_response_code(404);
                echo json_encode(["error" => "Venta no encontrada"]);
                exit;
            }

            echo json_encode([
                'venta' => $venta,
                'db' => $connectionType
            ]);
            exit;
        }
        else {
            http_response_code(400);
            echo json_encode(["error" => "Acción inválida"]);
            exit;
        }
    }
    catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error durante la operación: " . $e->getMessage()]);
    }
?>