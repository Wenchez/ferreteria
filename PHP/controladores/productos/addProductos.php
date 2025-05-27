<?php
    require __DIR__ . '/../../connections.php';

    header('Content-Type: application/json');

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "status"  => "error",
            "message" => "Acceso inválido. Solo se permiten solicitudes POST."
        ]);
        exit;
    }

    // 1. Obtener campo de elección de base de datos (por POST o GET)
    $dbChoice = $_GET['db_choice'] ?? $_POST['db_choice'] ?? 'local';

    // 2. Selección de conexión
    if ($dbChoice === 'remote') {
        $dbConnection   = $atlasConexion;
        $connectionType = 'REMOTE';
    } else {
        $dbConnection   = $localConexion;
        $connectionType = 'LOCAL';
    }

    if (!$dbConnection) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "No se pudo establecer conexión con la base de datos seleccionada."
        ]);
        exit;
    }

    // 3. Obtener campos del formulario
    $productName  = trim($_POST['productName']   ?? '');
    $category     = trim($_POST['category']      ?? '');
    $supplierName = trim($_POST['supplierName']  ?? '');
    $stock        = isset($_POST['stock'])       ? (int)   $_POST['stock']   : null;
    $price        = isset($_POST['price'])       ? (float) $_POST['price']   : null;

    // 4. Validaciones básicas
    if ($productName === '' || $category === '' || $supplierName === '' || $stock === null || $price === null) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Faltan campos requeridos o están vacíos."
        ]);
        exit;
    }

    try {
        $db               = $dbConnection->selectDatabase('ferreteria');
        $collectionSup    = $db->selectCollection('suppliers');
        $foundSupplier    = $collectionSup->findOne(['supplierName' => $supplierName]);

        if (!$foundSupplier) {
            http_response_code(400);
            echo json_encode([
                "status"  => "error",
                "message" => "Proveedor '$supplierName' no existe en la base seleccionada ($connectionType)."
            ]);
            exit;
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Error al verificar proveedor en $connectionType: " . $e->getMessage()
        ]);
        exit;
    }

    // 5. Generar un _id único con formato "PROD_XXX"
    function generateUniqueProductId($collection) {
        $count   = $collection->countDocuments();
        $counter = $count + 1;

        do {
            $candidate = sprintf("PROD_%03d", $counter);
            $exists    = $collection->findOne(['_id' => $candidate]);
            if ($exists === null) {
                return $candidate;
            }
            $counter++;
        } while (true);
    }

    try {
        $collectionProd = $db->selectCollection('products');
        $newId          = generateUniqueProductId($collectionProd);

        // 6. Construir el documento con el _id generado
        $newProduct = [
            '_id'          => $newId,
            'productName'  => $productName,
            'category'     => $category,
            'supplierName' => $supplierName,
            'stock'        => $stock,
            'price'        => $price
        ];

        // 7. Insertar en la base elegida
        $insertResult = $collectionProd->insertOne($newProduct);

        http_response_code(200);
        echo json_encode([
            "status"      => "success",
            "inserted_id" => $newId,
            "db"          => $connectionType
        ]);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Error al insertar en $connectionType: " . $e->getMessage()
        ]);
    }
?>