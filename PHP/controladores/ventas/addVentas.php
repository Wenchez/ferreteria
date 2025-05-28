<?php
require __DIR__ . '/../../connections.php';
session_start();
header('Content-Type: application/json');

// 1) Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status"  => "error",
        "message" => "Acceso inválido. Solo se permiten solicitudes POST."
    ]);
    exit;
}

// 2) Verificar sesión
if (
    !isset($_SESSION['UserID'])   || trim($_SESSION['UserID']) === '' ||
    !isset($_SESSION['Username']) || trim($_SESSION['Username']) === ''
) {
    http_response_code(401);
    echo json_encode([
        "status"  => "error",
        "message" => "No se encontró sesión activa. Debes iniciar sesión."
    ]);
    exit;
}
$userIdStr = $_SESSION['UserID'];
$userName  = $_SESSION['Username'];

// 3) Leer JSON entrante
$rawBody = file_get_contents('php://input');
$data    = json_decode($rawBody, true);
if ($data === null) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "JSON inválido o malformado."
    ]);
    exit;
}

// 4) Obtener elección de base de datos (GET o en JSON)
$dbChoice = $_GET['db_choice'] ?? $data['db_choice'] ?? 'local';
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

// 5) Validar campos obligatorios
$clientName = trim($data['clientName'] ?? '');
$itemsArray = $data['items']     ?? [];
if ($clientName === '' || !is_array($itemsArray) || count($itemsArray) === 0) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "Faltan campos obligatorios: 'clientName' o 'items' está vacío."
    ]);
    exit;
}

// 6) Convertir userId a ObjectId
try {
    $userIdObj = new MongoDB\BSON\ObjectId($userIdStr);
} catch (\Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "El UserID en sesión no es un ObjectId válido."
    ]);
    exit;
}

// 7) Procesar cada ítem: validar, convertir y armar documento de venta
$itemsDocument = [];
$totalSum = 0.0;

foreach ($itemsArray as $idx => $item) {
    if (
        !isset($item['productId'])   ||
        !isset($item['productName']) ||
        !isset($item['quantity'])    ||
        !isset($item['unitPrice'])
    ) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "El item en índice $idx carece de 'productId', 'productName', 'quantity' o 'unitPrice'."
        ]);
        exit;
    }

    $productIdStr = trim($item['productId']);
    if ($productIdStr === '') {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "El productId está vacío en índice $idx."
        ]);
        exit;
    }

    $productName = trim($item['productName']);
    $quantity    = (int)   $item['quantity'];
    $unitPrice   = (float) $item['unitPrice'];

    if ($productName === '' || $quantity < 1 || $unitPrice < 0) {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Valores inválidos en item índice $idx."
        ]);
        exit;
    }

    $subTotal = $quantity * $unitPrice;
    $totalSum += $subTotal;

    $itemsDocument[] = [
        'productId'   => $productIdStr,
        'productName' => $productName,
        'quantity'    => $quantity,
        'unitPrice'   => $unitPrice,
        'subTotal'    => $subTotal
    ];
}

// 8) Calcular IVA y total de la venta
$ivaVenta   = round($totalSum * 0.16, 2);
$totalVenta = round($totalSum + $ivaVenta, 2);

// 9) Generar saleDate
$saleDateBson = new MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000));

// 10) Obtener nuevo ID de venta usando la base seleccionada
try {
    $db       = $dbConnection->selectDatabase('ferreteria');
    $salesCol = $db->selectCollection('sales');
    $newSaleId = generateUniqueSaleId($salesCol);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Error al generar ID de venta: " . $e->getMessage()
    ]);
    exit;
}

// 11) Armar documento completo de la venta
$saleDocument = [
    '_id'        => $newSaleId,
    'saleDate'   => $saleDateBson,
    'subTotal'   => (double) $totalSum,
    'iva'        => (double) $ivaVenta,
    'total'      => (double) $totalVenta,
    'userId'     => $userIdObj,
    'userName'   => $userName,
    'clientName' => $clientName,
    'items'      => $itemsDocument
];

// 12) Insertar en la base seleccionada
try {
    $res = $salesCol->insertOne($saleDocument);
    // 13) Si la venta se guardó, actualizar stock de cada producto solo en la misma base
    foreach ($itemsDocument as $item) {
        $productIdStr    = $item['productId'];
        $cantidadVendida = $item['quantity'];

        // Obtener stock actual
        $stockActual = getCurrentStock($dbConnection, $productIdStr);
        if ($stockActual !== null) {
            $nuevoStock = max(0, $stockActual - $cantidadVendida);
            callUpdateStockEndpoint($productIdStr, $nuevoStock, $dbChoice);
        }
    }

    http_response_code(200);
    echo json_encode([
        "status"      => "success",
        "inserted_id" => $newSaleId,
        "db"          => $connectionType
    ]);
    exit;
} catch (MongoDB\Driver\Exception\Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Error al insertar venta en $connectionType: " . $e->getMessage()
    ]);
    exit;
}
// Funciones Auxiliares

// Función para obtener stock actual de un producto (ahora acepta string)
function getCurrentStock($conexion, string $productId) {
    $db = $conexion->selectDatabase('ferreteria');
    $col = $db->selectCollection('products');
    $prod = $col->findOne(['_id' => $productId]);
    return isset($prod['stock']) ? (int)$prod['stock'] : null;
}

// Función para llamar a subProd.php y actualizar stock
function callUpdateStockEndpoint(string $productIdStr, int $newStock, string $dbChoice) {
    $url = "http://localhost/ferreteria/PHP/controladores/ventas/subProd.php";
    $data = http_build_query([
        'product_id'         => $productIdStr,
        'quantityActualized' => $newStock,
        'db_choice'         => $dbChoice
    ]);
    $opts = [
        'http' => [
            'method'  => 'PUT',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
                       . "Content-Length: " . strlen($data) . "\r\n",
            'content' => $data
        ]
    ];
    $context = stream_context_create($opts);
    @file_get_contents($url, false, $context);
}

// Función para generar ID tipo TICKET-00001
function generateUniqueSaleId($collection) {
    $count = $collection->countDocuments();
    $counter = $count + 1;
    do {
        $candidate = sprintf("TICKET-%05d", $counter);
        $exists = $collection->findOne(['_id' => $candidate]);
        if ($exists === null) {
            return $candidate;
        }
        $counter++;
    } while (true);
}
?>