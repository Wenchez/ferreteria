$(document).ready(function() {
    $('#btnPrintTicket').on('click', function(e) {
        e.preventDefault();
        const saleId = $(this).data('id');
        ticket();
    });
});

function ticket(){
    // 1) Recoger valores simples
    const ticketNumber = $('#ticketNumber').val();
    const saleDate     = $('#saleDate').val();
    const saleTime     = $('#saleTime').val();
    const seller       = $('#seller').val();
    const client       = $('#client').val();

    // 2) Recoger productos leyendo cada fila del contenedor
    const products = [];
    $('#productDetailsContainer .mb-2').each(function() {

        const nombre = $(this).find('> div:nth-child(1)').text().trim();
        const texto  = $(this).find('> div.text-end').text().trim();

        const parts = texto.split('x');

        const cantidad = parseInt(parts[0].trim());

        const priceText = parts[1].split('=')[0].replace('$', '').trim();
        const price     = parseFloat(priceText);

        products.push({
            name: nombre,
            quantity: cantidad,
            price: price
        });
    });

    // 3) Calcular subtotal, IVA y total en JS (aunque podrías hacerlo en PHP)
    let subtotal = 0;
    products.forEach(p => {
        subtotal += p.quantity * p.price;
    });
    const iva   = parseFloat((subtotal * 0.16).toFixed(2));
    const total = parseFloat((subtotal + iva).toFixed(2));

    console.log(ticketNumber)

    // 4) Mandar todo por AJAX a ticket.php
    $.ajax({
        url: '/ferreteria/PHP/ticket.php',
        method: 'POST',
        data: {
            ticketNumber: ticketNumber,
            saleDate: saleDate,
            saleTime: saleTime,
            seller: seller,
            client: client,
            products: JSON.stringify(products),
            subtotal: subtotal,
            iva: iva,
            total: total
        },
        xhrFields: {
            responseType: 'blob' // para recibir el PDF como blob
        },
        success: function(blob) {
            // Recibimos el PDF como un Blob. Vamos a mostrarlo en una pestaña nueva:
            const url = URL.createObjectURL(blob);
            window.open(url);
        },
        error: function() {
            alert('Error al generar el PDF.');
        }
    });
}