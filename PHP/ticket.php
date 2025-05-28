<?php
    // 1. Incluir Dompdf
    require_once __DIR__ . '/dompdf/autoload.inc.php';
    use Dompdf\Dompdf;

    // Recoger todos los valores de $_POST
    if (
        !isset($_POST['ticketNumber'], $_POST['saleDate'], $_POST['saleTime'],
                $_POST['seller'], $_POST['client'],
                $_POST['products'], $_POST['subtotal'],
                $_POST['iva'], $_POST['total'])
    ) {
        die('Faltan datos');
    }

    $ticketNumber = $_POST['ticketNumber'];
    $saleDate     = $_POST['saleDate'];
    $saleTime     = $_POST['saleTime'];
    $seller       = $_POST['seller'];
    $client       = $_POST['client'];

    // Decodificamos el JSON de productos
    $productsJson = $_POST['products'];
    $products = json_decode($productsJson, true);
    if (!is_array($products)) {
        die('Formato de productos inválido');
    }

    $subtotal = floatval($_POST['subtotal']);
    $iva      = floatval($_POST['iva']);
    $total    = floatval($_POST['total']);

    // 2. Recopilar datos (por ejemplo, de POST o base de datos) y generar el HTML
    //    Supongamos que ya tienes variables como $ticketNumber, $saleDate, $products (un array), $subtotal, $iva, $total.
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <style>
        /* Puedes copiar aquí las partes esenciales de Bootstrap que uses */
        body { font-family: sans-serif; font-size: 12px; }
        .row { display: flex; margin-bottom: 0.5rem; }
        .col-sm-4 { width: 33.3333%; font-weight: bold; }
        .col-sm-8 { width: 66.6666%; text-align: right; }
        .fw-bold { font-weight: bold; }
        hr { border: none; border-top: 1px solid #ccc; margin: 0.5rem 0; }
        /* Ajusta otros estilos según necesites */
    </style>
    </head>
    <body>
    <h2>Detalles de venta</h2>
    <div class="row">
        <div class="col-sm-4">TICKET:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($ticketNumber); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4">FECHA:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($saleDate); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4">HORA:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($saleTime); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4">VENDEDOR:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($seller); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4">CLIENTE:</div>
        <div class="col-sm-8"><?php echo htmlspecialchars($client); ?></div>
    </div>

    <hr>

    <h3>Productos</h3>
    <?php foreach ($products as $p): ?>
        <div class="row">
        <div class="col-sm-4"><?php echo htmlspecialchars($p['name']); ?></div>
        <div class="col-sm-8">
            <?php echo intval($p['quantity']); ?> × $<?php echo number_format($p['price'], 2); ?> =
            $<?php echo number_format($p['quantity'] * $p['price'], 2); ?>
        </div>
        </div>
    <?php endforeach; ?>

    <hr>

    <div class="row">
        <div class="col-sm-4">SUBTOTAL:</div>
        <div class="col-sm-8">$<?php echo number_format($subtotal, 2); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4">IVA (16%):</div>
        <div class="col-sm-8">$<?php echo number_format($iva, 2); ?></div>
    </div>
    <div class="row">
        <div class="col-sm-4 fw-bold">TOTAL:</div>
        <div class="col-sm-8 fw-bold">$<?php echo number_format($total, 2); ?></div>
    </div>
    </body>
    </html>
<?php
    $html = ob_get_clean();

    // 3. Instanciar Dompdf y cargar el HTML
    $dompdf = new Dompdf();
    // Ajusta el tamaño de papel si quieres (ej. 'A4', 'letter', etc.) y orientación:
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->loadHtml($html);

    // 4. Renderizar
    $dompdf->render();

    // Para mostrar en el navegador (inline):
    $dompdf->stream('ticket_' . $ticketNumber . '.pdf', [
        'Attachment' => false  // true = descarga forzada
    ]);
    exit;
?>