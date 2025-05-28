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

        switch ($action) {
            case "getSales":
                // Caso 1: devolver todos los productos
                $clientName = $_GET['clientName'] ?? '';
                $date = $_GET['date'] ?? '';

                $filter = [];

                // Filtro por nombre (si se proporcionó)
                if (!empty($clientName)) {
                    $filter['clientName'] = new MongoDB\BSON\Regex("^$clientName", 'i');
                }

                // Filtro por fecha si es 'today'
                if ($date === 'today') {
                    $start = new DateTime('today', new DateTimeZone('America/Mexico_City'));
                    $end = clone $start;
                    $end->modify('+1 day');

                    $filter['saleDate'] = [
                        '$gte' => new MongoDB\BSON\UTCDateTime($start->getTimestamp() * 1000),
                        '$lt'  => new MongoDB\BSON\UTCDateTime($end->getTimestamp() * 1000)
                    ];
                }

                try {
                    $documentos = $coleccion->find($filter);

                    $ventas = [];
                    foreach ($documentos as $venta) {
                        $ventas[] = $venta;
                    }
                    echo json_encode([
                        'filter' => $filter,
                        'ventas' => $ventas,
                        'db'     => $connectionType
                    ]);
                } catch (Exception $e) {
                    echo json_encode("Error durante la operación: " . $e->getMessage());
                }
                break;

            case "getSaleById":
                // Caso 2: devolver un solo producto según su ID
                $ventaId = $_GET['ventaId'] ?? '';
                if (empty($ventaId)) {
                    http_response_code(400);
                    echo json_encode(["error" => "No se proporcionó productId"]);
                    exit;
                }

                $filtro = ['_id' => $ventaId];
                $venta  = $coleccion->findOne($filtro);

                if (!$venta) {
                    http_response_code(404);
                    echo json_encode(["error" => "Venta no encontrada"]);
                    exit;
                }

                echo json_encode([
                    'venta' => $venta,
                    'db'    => $connectionType
                ]);
                break;

            default:
                http_response_code(400);
                echo json_encode(["error" => "Acción inválida"]);
                break;
        }
    }
    catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error durante la operación: " . $e->getMessage()]);
    }
?>