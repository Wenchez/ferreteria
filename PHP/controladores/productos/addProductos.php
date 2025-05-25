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

// 1. Obtener campos del formulario
$productName  = trim($_POST['productName']   ?? '');
$category     = trim($_POST['category']      ?? '');
$supplierName = trim($_POST['supplierName']  ?? '');
$stock        = isset($_POST['stock'])       ? (int) $_POST['stock']   : null;
$price        = isset($_POST['price'])       ? (float) $_POST['price'] : null;

// 2. Validaciones básicas
if ($productName === '' || $category === '' || $supplierName === '' || $stock === null || $price === null) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "Faltan campos requeridos o están vacíos."
    ]);
    exit;
}

// 3. Verificar que el supplierName exista en alguna de las dos bases
$supplierFilter = ['supplierName' => $supplierName];
$supplierExistsLocal  = false;
$supplierExistsRemote = false;
$checkLocalMsg        = '';
$checkRemoteMsg       = '';

if ($localConexion) {
    try {
        $dbLocal          = $localConexion->selectDatabase('ferreteria');
        $collectionSupLoc = $dbLocal->selectCollection('suppliers');
        $foundLocal       = $collectionSupLoc->findOne($supplierFilter);
        if ($foundLocal) {
            $supplierExistsLocal = true;
        } else {
            $checkLocalMsg = "Proveedor '$supplierName' no existe en LOCAL.";
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $checkLocalMsg = "Error al verificar proveedor en LOCAL: " . $e->getMessage();
    }
} else {
    $checkLocalMsg = "Conexión LOCAL no disponible para verificar proveedor.";
}

if ($atlasConexion) {
    try {
        $dbAtlas           = $atlasConexion->selectDatabase('ferreteria');
        $collectionSupAtl  = $dbAtlas->selectCollection('suppliers');
        $foundRemote       = $collectionSupAtl->findOne($supplierFilter);
        if ($foundRemote) {
            $supplierExistsRemote = true;
        } else {
            $checkRemoteMsg = "Proveedor '$supplierName' no existe en REMOTO.";
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $checkRemoteMsg = "Error al verificar proveedor en REMOTO: " . $e->getMessage();
    }
} else {
    $checkRemoteMsg = "Conexión REMOTA no disponible para verificar proveedor.";
}

if (!$supplierExistsLocal && !$supplierExistsRemote) {
    http_response_code(400);
    echo json_encode([
        "status"         => "error",
        "message_local"  => $checkLocalMsg,
        "message_remote" => $checkRemoteMsg
    ]);
    exit;
}

// 4. Generar un _id único con formato "PROD_XXX"
function generateUniqueProductId($collection) {
    // Tomar la cantidad total de documentos como punto de partida
    $count = $collection->countDocuments();
    $counter = $count + 1;

    do {
        $candidate = sprintf("PROD_%03d", $counter);
        $exists = $collection->findOne(['_id' => $candidate]);
        if ($exists === null) {
            return $candidate;
        }
        $counter++;
    } while (true);
}

// Primero, generar el ID usando la colección LOCAL (cualquiera sirve, pero tomamos LOCAL si está disponible)
if ($localConexion) {
    $collectionProdLoc = $dbLocal->selectCollection('products');
    $newId = generateUniqueProductId($collectionProdLoc);
} elseif ($atlasConexion) {
    $collectionProdAtl = $dbAtlas->selectCollection('products');
    $newId = generateUniqueProductId($collectionProdAtl);
} else {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Ninguna conexión a BD está disponible para generar ID."
    ]);
    exit;
}

// 5. Construir el documento con el _id generado
$newProduct = [
    '_id'          => $newId,
    'productName'  => $productName,
    'category'     => $category,
    'supplierName' => $supplierName,
    'stock'        => $stock,
    'price'        => $price
];

$insertLocalMsg  = '';
$insertRemoteMsg = '';
$insertSuccess   = false;

// 6. Intentar insertar en LOCAL usando el mismo _id
if ($localConexion) {
    try {
        $resLocal = $collectionProdLoc->insertOne($newProduct);
        $insertLocalMsg  = "Producto insertado en LOCAL (ID: $newId).";
        $insertSuccess   = true;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $insertLocalMsg = "Error al insertar en LOCAL: " . $e->getMessage();
    }
} else {
    $insertLocalMsg = "Conexión LOCAL no disponible. No se pudo insertar.";
}

// 7. Intentar insertar en REMOTO usando el mismo _id
if ($atlasConexion) {
    try {
        $collectionProdAtl = $dbAtlas->selectCollection('products');
        $resRemote = $collectionProdAtl->insertOne($newProduct);
        $insertRemoteMsg  = "Producto insertado en REMOTO (ID: $newId).";
        $insertSuccess    = true;
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $insertRemoteMsg = "Error al insertar en REMOTO: " . $e->getMessage();
    }
} else {
    $insertRemoteMsg = "Conexión REMOTA no disponible. No se pudo insertar.";
}

// 8. Devolver respuesta
if ($insertSuccess) {
    http_response_code(200);
    echo json_encode([
        "status"         => "success",
        "inserted_id"    => $newId,
        "message_local"  => $insertLocalMsg,
        "message_remote" => $insertRemoteMsg
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status"         => "error",
        "message_local"  => $insertLocalMsg,
        "message_remote" => $insertRemoteMsg
    ]);
}
?>