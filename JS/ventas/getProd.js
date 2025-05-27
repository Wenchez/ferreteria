function getProductoByName(productName){
    const dbChoice = $('#databaseSwitch').val();
    $.get(
      "/ferreteria/PHP/controladores/productos/getProductos.php",
      {
        db_choice: dbChoice,
        action: "getProductByName",
        productName: productName,
      },
      function () {
      })
      .done(function (response) {
        console.log("Productos recibidos:", response.producto);
        console.log("Usando:", response.db);

        // Si el stock es 0, mostrar mensaje de "Producto agotado" y salir
        if (response.producto.stock === 0) {
            $("#alert_buscar")
                .text("Producto agotado.")
                .removeClass("d-none")
                .addClass("show");
            return;
        }

        const $tbody = $("table.table tbody");

        const $tr = $("<tr>");

        // Código
        $tr.append($("<td>").text(response.producto._id));
        // Producto
        $tr.append($("<td>").text(response.producto.productName));
        // Precio
        $tr.append(
            $("<td>")
                .addClass("text-nowrap")
                .text(`$${response.producto.price.toFixed(2)}`)
        );

        // Cantidad (input numérico que empieza en 1 y como máximo el stock)
		const $cantidadTd = $("<td>");
		const $inputCantidad = $(`
		<input type="number" class="form-control cantidad-input"
				min="1" max="${response.producto.stock}" value="1"
				data-precio="${response.producto.price}" data-product-name="${productName}">
		`);
		$cantidadTd.append($inputCantidad);
		$tr.append($cantidadTd);

        // Subtotal (precio * 1 por defecto)
        const subtotalInicial = response.producto.price * 1;
        $tr.append(
            $("<td>")
                .addClass("text-nowrap")
                .text(`$${subtotalInicial.toFixed(2)}`)
        );

        // Acciones (solo botón quitar con ícono)
        const $accionesTd = $("<td>").addClass("text-center");
        const $btnQuitar = $(`
            <button class="btn btn-outline-danger btn-quitar" title="Quitar">
                <i class="bi bi-trash"></i>
            </button>
        `);
        $accionesTd.append($btnQuitar);
        $tr.append($accionesTd);

        // Agregamos la fila
        $tbody.append($tr);

		$("#add_prod").val('');
		updateSummary();
    })
      .fail(function (jqXHR, textStatus, errorThrown) {
		$("#alert_buscar")
                .text("El producto no existe.")
                .addClass("show")
                .removeClass("d-none");
            return;
      });
}