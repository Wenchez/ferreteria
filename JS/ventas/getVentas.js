$(document).ready(function () {
    getventas();

    $('input[name="databaseType"]').on("change", function () {
        getventas();
    });

    $(document).on("click", ".btn-detalle", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");

        // Asignar el id al input oculto
        $("#editId").val(id);
        const ventaId = $(this).closest("tr").data("id");
        console.log("ID del venta a ver:", ventaId);
        getVentaByID(ventaId);
    });

    $(document).on("click", ".btn-editar", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");

        // Asignar el id al input oculto
        $("#editId").val(id);
        const ventaId = $(this).closest("tr").data("id");
        console.log("ID del venta a editar:", ventaId);
        //getVentaByID(productId);
    });

    $(document).on("click", ".btn-eliminar", function () {
        const productId = $(this).closest("tr").data("id");
        console.log("ID del venta a eliminar:", productId);
    });
});

function getventas() {
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/ventas/getVentas.php', 
          { db_choice: dbChoice, action: 'getSales' })
    .done(function(response) {
        console.log("ventas recibidos:", response.ventas);
        console.log("Usando:", response.db);

        // 1) Seleccionamos el <tbody> y lo vaciamos
        const $tbody = $("table.table tbody");
        $tbody.empty();

        // 2) Recorremos el array de ventas
        response.ventas.forEach(function(venta) {
            const $tr = $("<tr>");

            // Fecha
            const timestamp = parseInt(venta.saleDate.$date.$numberLong);
            const fechaFormateada = formatearFechaTotal(new Date(timestamp));
            $tr.append($("<td>").text(fechaFormateada));

            // Vendedor
            $tr.append($("<td>").text(venta.userName));

            // Cliente
            $tr.append($("<td>").text(venta.clientName));

            // Cantidad total de productos vendidos (sumamos las cantidades de todos los items)
            const cantidadProductos = venta.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
            $tr.append($("<td>").text(cantidadProductos));

            // SUBTOTAL: si existe venta.subTotal, lo usamos; si no, lo sumamos de los ítems
            let subTotalValue;
            if (venta.subTotal !== undefined && venta.subTotal !== null) {
                subTotalValue = parseFloat(venta.subTotal);
            } else {
                subTotalValue = venta.items
                    .reduce((sum, item) => sum + (parseFloat(item.subTotal) || 0), 0);
            }
            $tr.append(
                $("<td>")
                    .addClass("text-nowrap")
                    .text(`$${subTotalValue.toFixed(2)}`)
            );

            // TOTAL (se asume que siempre existe venta.total)
            const totalValue = parseFloat(venta.total || subTotalValue);
            $tr.append(
                $("<td>")
                    .addClass("text-nowrap")
                    .text(`$${totalValue.toFixed(2)}`)
            );

            // Acciones (botones)
            const $accionesTd = $("<td>").addClass("d-flex text-wrap");
            const $btnDetalles  = $(`<button class="btn btn-success me-1 btn-detalle" data-id="${venta._id}" data-bs-toggle="modal" data-bs-target="#detailSaleModal">Detalles</button>`);
            const $btnEliminar  = $(`<button class="btn btn-danger btn-eliminar" data-id="${venta._id}">Eliminar</button>`);
            $accionesTd.append($btnDetalles, $btnEliminar);
            $tr.append($accionesTd);

            // Asignar _id a la fila
            $tr.attr("data-id", venta._id);

            // Insertar en el tbody
            $tbody.append($tr);
        });
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener ventas:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}

function getVentaByID(ventaId) {
    const dbChoice = $('input[name="databaseType"]:checked').val();

    $.get(
        '/ferreteria/PHP/controladores/ventas/getVentas.php',
        { db_choice: dbChoice, action: 'getSaleById', ventaId: ventaId }
    )
    .done(function(response) {
        // response.venta es el objeto con la venta completa
        const venta = response.venta;
        if (!venta) {
        console.error("Venta no encontrada en respuesta.");
        return;
        }

        // 1) TICKET
        $('#ticketNumber').val(venta._id);

        // 2) Fecha y Hora (MongoDB Extended JSON { $date: { $numberLong: "..." } })
        const ts = parseInt(venta.saleDate.$date.$numberLong);
        const fechaObj = new Date(ts);

        $('#saleDate').val(formatearFecha(fechaObj));
        $('#saleTime').val(formatearHora(fechaObj));

        // 3) Vendedor y Cliente
        $('#seller').val(venta.userName);
        $('#client').val(venta.clientName);

        // 4) Productos
        //    Vamos a vaciar primero el contenedor y luego, para cada item,
        //    crear un bloque con dos <div> o <p>.
        const $container = $('#productDetailsContainer');
        $container.empty();

        // Calculamos subtotal total de todos los items (solo efecto de cálculo)
        let subtotalCalculado = 0;

        venta.items.forEach(function(item) {
            // Bloque contenedor para un ítem (dos líneas)
            const $bloqueProducto = $('<div class="mb-2">');

            const precio = parseFloat(item.unitPrice ?? 0);
            const cantidad = parseFloat(item.quantity ?? 0);
            const subtotal = precio * cantidad;

            const $linea1 = $('<div>').text(`${item.productName}`);
            const $linea2 = $('<div class="text-end">').text(`${cantidad} x $${precio.toFixed(2)} =   $${subtotal.toFixed(2)}`);

            $bloqueProducto.append($linea1, $linea2);
            $container.append($bloqueProducto);
        });

        // 5) Subtotal (del cálculo anterior)
        $('#subtotal').val(`$${venta.subTotal.toFixed(2)}`);

        // 6) IVA (16% sobre el subtotal)
        const iva = venta.subTotal * 0.16;
        $('#iva').val(`$${iva.toFixed(2)}`);

        // 7) Total (lo tomamos directamente de venta.total)
        $('#total').val(`$${venta.total.toFixed(2)}`);

        // 8) Mostrar el modal (Bootstrap 5)
        const modalEl = document.getElementById('detailSaleModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener venta por ID:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}

//Funcion auxiliar para la fecha
function formatearFechaTotal(fechaISO) {
    const fecha = new Date(fechaISO);
    const dia = fecha.getDate().toString().padStart(2, '0');
    const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
    const anio = fecha.getFullYear();
    const horas = fecha.getHours().toString().padStart(2, '0');
    const minutos = fecha.getMinutes().toString().padStart(2, '0');
    
    return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
}

// Función para formatear fecha a DD/MM/YYYY
function formatearFecha(fecha) {
  const dia     = fecha.getDate().toString().padStart(2, '0');
  const mes     = (fecha.getMonth() + 1).toString().padStart(2, '0');
  const anio    = fecha.getFullYear();
  return `${dia}/${mes}/${anio}`;
}

// Función para formatear hora a HH:MM
function formatearHora(fecha) {
  const horas   = fecha.getHours().toString().padStart(2, '0');
  const minutos = fecha.getMinutes().toString().padStart(2, '0');
  return `${horas}:${minutos}`;
}