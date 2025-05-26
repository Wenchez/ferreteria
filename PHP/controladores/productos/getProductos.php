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
        $coleccion = $db->selectCollection('products');

        if ($action === "getProducts") {
            // Caso 1: devolver todos los productos
            try{
                $documentos = $coleccion->find();

                $productos = [];
                foreach ($documentos as $producto) {
                    $stock = $producto['stock'] ?? 0; // Si no tiene stock definido, asumimos 0

                    // Evaluar el estado en base al stock
                    if ($stock == 0) {
                        $producto['state'] = 'Agotado';
                    } elseif ($stock < 10) {
                        $producto['state'] = 'Poco stock';
                    } elseif ($stock > 100) {
                        $producto['state'] = 'Abundante';
                    }else {
                        $producto['state'] = 'Normal';
                    }

                    $productos[] = $producto;
                }
                echo json_encode([
                    'productos'=>$productos,
                    'db'=>$connectionType
                ]);
            }
            catch (Exception $e){
                echo json_encode("Error durante la operación: " . $e->getMessage());
            }
            exit;
        }
        elseif ($action === "getProductById") {
            // Caso 2: devolver un solo producto según su ID
            $productId = $_GET['productId'] ?? '';
            if (empty($productId)) {
                http_response_code(400);
                echo json_encode(["error" => "No se proporcionó productId"]);
                exit;
            }

            // Construir el filtro y los datos a actualizar
            $filtro = ['_id' => $productId];
            $producto = $coleccion->findOne($filtro);

            if (!$producto) {
                http_response_code(404);
                echo json_encode(["error" => "Producto no encontrado"]);
                exit;
            }

            $stock = $producto['stock'] ?? 0;
            if ($stock == 0) {
                $producto['state'] = 'Agotado';
            } elseif ($stock < 10) {
                $producto['state'] = 'Poco stock';
            } elseif ($stock > 100) {
                $producto['state'] = 'Abundante';
            } else {
                $producto['state'] = 'Normal';
            }

            echo json_encode([
                'producto' => $producto,
                'db' => $connectionType
            ]);
            exit;
        } elseif ($action === "getProductByName") {
            // Caso 2: devolver un solo producto según su ID
            $productName = $_GET['productName'] ?? '';
            if (empty($productName)) {
                http_response_code(400);
                echo json_encode(["error" => "No se proporcionó productName"]);
                exit;
            }

            // Construir el filtro y los datos a actualizar
            $filtro = ['productName' => $productName];
            $producto = $coleccion->findOne($filtro);

            if (!$producto) {
                http_response_code(404);
                echo json_encode(["error" => "Producto no encontrado"]);
                exit;
            }

            echo json_encode([
                'productName' => $productName,
                'producto' => $producto,
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