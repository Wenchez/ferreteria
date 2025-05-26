$(document).ready(function () {
    updateSummary();

    setupPaymentButtonControl();

    $(document).on("click", "#prod_search", function () {
        addProd();
    });

    $(document).on("input", "#add_prod", function () {
        $("#alert_buscar").text("El producto no existe.").addClass("d-none").removeClass("show");
    });

    // Borrar producto
    $(document).on("click", ".btn-quitar", function () {
        $(this).closest("tr").remove();
        updateSummary();
    });

    // Maneja cambios en cualquier input de cantidad
    $(document).on("input", ".cantidad-input", function () {
        const $input = $(this);
        modTicket($input);
    });

    // Crear o procesar venta
    $(document).on("click", "#processPaymentButton", function () {
        addSale();
    });
});

function addSale(){
    const clientName = $("#clientSearchInput").val().trim();
    const itemsList  = buildItemsArray();

    if (clientName === "" || itemsList.length === 0) {
        alert("Debes ingresar un nombre de cliente y al menos un producto.");
        return;
    }

    const payload = {
        clientName: clientName,
        items:      itemsList
    };

    console.log(payload);

    $.ajax({
        url: '/ferreteria/PHP/controladores/ventas/addVentas.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function (res) {
        if (res.status === 'success') {
            clearAllFields();
            // Crear y mostrar el toast
            const toastHtml = `
                <div class="toast align-items-center text-white bg-success border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Venta registrada correctamente.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            const $toast = $(toastHtml);
            $(".toast-container").append($toast);

            // Mostrar el toast con Bootstrap
            const toast = new bootstrap.Toast($toast[0]);
            toast.show();
        } else {
            console.error("Error al insertar:", res);
            alert("Ocurrió un error: " + JSON.stringify(res));
        }
        },
        error: function (jqXHR) {
        console.error("Error de servidor:", jqXHR.responseText);
        alert("Error en la petición al servidor.");
        }
    });
}

function addProd(){
    const productName = $("#add_prod").val().trim();

    if (productName !== "") {
        // Buscar si el producto ya está en la tabla
        let repetido = false;
        $("#saleItemsTableBody tr").each(function () {
            const nombreEnTabla = $(this).find("td:eq(1)").text().trim();
            if (nombreEnTabla.toLowerCase() === productName.toLowerCase()) {
                repetido = true;
                return false; // salir del .each()
            }
        });

        if (repetido) {
            $("#alert_buscar")
                .text("El producto ya está en la tabla.")
                .addClass("show")
                .removeClass("d-none");
        } else {
            $("#alert_buscar").addClass("d-none"); // ocultar si estaba visible
            console.log("Nombre del producto:", productName);
            getProductoByName(productName);
        }
    } else {
        console.log("No hay producto tonto");
    }
}

function updateSummary() {
    let subtotal = 0;
    const $tbody = $("#saleItemsTableBody");

    // Eliminar cualquier mensaje anterior
    $tbody.find(".mensaje-productos").remove();

    const filas = $tbody.find("tr");
    const tieneProductos = filas.length > 0;

    // Si no hay productos, mostrar mensaje
    if (!tieneProductos) {
        const $mensaje = $('<tr class="mensaje-productos"><td colspan="6" class="text-center fw-bold text-success">Agregue al menos un producto</td></tr>');
        $tbody.append($mensaje);
    }

    // Recalcular totales solo si hay productos
    filas.each(function () {
        const textoSubtotal = $(this).find("td").eq(4).text().trim();
        if (textoSubtotal) {
            const valor = parseFloat(textoSubtotal.replace(/[^0-9.-]+/g, "")) || 0;
            subtotal += valor;
        }
    });

    // Mostrar los valores, incluso si son 0
    $("#summarySubtotal").text(`$${subtotal.toFixed(2)}`);
    const iva = subtotal * 0.16;
    $("#summaryIva").text(`$${iva.toFixed(2)}`);
    const total = subtotal + iva;
    $("#summaryTotal").text(`$${total.toFixed(2)}`);
}

function modTicket($input){
    const cantidad = parseInt($input.val()) || 0;
        const precio = parseFloat($input.data("precio")) || 0;
        const max = parseInt($input.attr("max")) || Infinity;
        // Buscar la fila y la celda de subtotal (columna 5 → índice 4)
        const $fila = $input.closest("tr");
        const $celdaSubtotal = $fila.find("td").eq(4); // Subtotal está en la 5ª columna
        if (cantidad > max) {
            $input.val(max); // Forzar al máximo
            const subtotalMax = max * precio;
            $celdaSubtotal.text(`$${subtotalMax.toFixed(2)}`); // Mostrar el nuevo subtotal corregido
            updateSummary();
            return;
        }
        if (cantidad < 1) {
            $input.val(1); // Forzar al máximo
            const subtotalMin = precio;
            $celdaSubtotal.text(`$${subtotalMin.toFixed(2)}`); // Mostrar el nuevo subtotal corregido
            updateSummary();
            return;
        }
        const subtotal = cantidad * precio;
        console.log(`Nuevo subtotal: ${cantidad} x $${precio.toFixed(2)} = $${subtotal.toFixed(2)}`);
        $celdaSubtotal.text(`$${subtotal.toFixed(2)}`);

        updateSummary();
}

function buildItemsArray() {
    const items = [];

    $("#saleItemsTableBody tr").each(function () {
        // Cada fila
        const $fila = $(this);

        // ID del producto (suponiendo que lo pusiste en <tr data-id="…">)
        const productId = $fila.find("td").eq(0).text().trim();

        // Input de cantidad
        const $input = $fila.find(".cantidad-input");

        // Obtener el nombre del producto (columna 2)
        //   —– o bien, si lo pusiste como atributo data-product-name en el input:
        const productName = $fila
        .find(".cantidad-input")
        .data("product-name")
        .toString();

        // Cantidad: valor actual del input
        const quantity = parseInt(
        $fila.find(".cantidad-input").val()
        ) || 0;

        // Precio unitario: lo guardamos en data-precio del input
        const unitPrice = parseFloat(
        $fila.find(".cantidad-input").data("precio")
        ) || 0.0;

         // Cantidad máxima permitida (atributo max del <input>)
        const maxQuantity = parseInt($input.attr("max")) || 0;

        // Solo agregamos si tenemos nombre, quantity ≥1 y precio ≥0
        if (productId && productName && quantity > 0 && unitPrice >= 0) {
            items.push({
                productId:   productId,
                productName: productName,
                quantity:    quantity,
                unitPrice:   unitPrice,
                maxQuantity: maxQuantity
            });
        }
        });

    return items;
}

function setupPaymentButtonControl() {
    const $clientInput = $('#clientSearchInput');
    const $itemsBody = $('#saleItemsTableBody');
    const $paymentButton = $('#processPaymentButton');

    function updateButtonState() {
        const clientEmpty = $.trim($clientInput.val()) === "";
        const itemsEmpty = $itemsBody.find('tr').length === 0;
        $paymentButton.prop('disabled', clientEmpty || itemsEmpty);
    }

    $clientInput.on('input', updateButtonState);

    const observer = new MutationObserver(updateButtonState);
    observer.observe($itemsBody[0], { childList: true });

    updateButtonState(); // Verificar al cargar
}