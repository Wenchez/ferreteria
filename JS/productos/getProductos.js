$(document).ready(function () {
    getProductos(); // Primer llamado para la tabla

    $('#databaseSwitch').on("change", function () {
        setTimeout(function() {
            getProductos(); // O la funcion que tengas para mostrar las cosas en tu tabla
        }, 50);
    });

    $(document).on("click", ".btn-editar", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");

        // Asignar el id al input oculto
        $("#editId").val(id);
        const productId = $(this).closest("tr").data("id");
        console.log("ID del producto a editar:", productId);
        getProductoByID(productId);
    });
});

function getProductos(){
    const dbChoice = $('#databaseSwitch:checked').val();
    $.get('/ferreteria/PHP/controladores/productos/getProductos.php', { db_choice: dbChoice, action:'getProducts' }, function() {
    })
    .done(function(response){
        console.log("Productos recibidos:", response.productos);
        console.log("Usando:", response.db);

         // 1) Seleccionamos el <tbody> y lo vaciamos
        const $tbody = $("table.table tbody");
        $tbody.empty();

        // 2) Recorremos el array de productos
        response.productos.forEach(function(prod) {
            // Creamos fila <tr>
            const $tr = $("<tr>");

            // 3) Rellenamos cada <td> con el campo correspondiente
            $tr.append($("<td>").text(prod.productName));
            $tr.append($("<td>").text(prod.category));
            $tr.append($("<td>").addClass("text-nowrap").text("$" + prod.price));
            $tr.append($("<td>").addClass("text-nowrap").text(prod.stock));
            $tr.append($("<td>").text(prod.supplierName));
            
            // Crear celda del estado
            const $estadoTd = $("<td>");

            // Crear el badge
            const $badge = $("<span>").addClass("text-nowrap badge fs-6 fw-bold");

            switch (prod.state) {
                case "Agotado":
                    $badge.addClass("bg-danger text-white").text("Agotado");
                    break;
                case "Poco stock":
                    $badge.addClass("bg-warning text-dark").text("Poco stock");
                    break;
                case "Abundante":
                    $badge.addClass("bg-success text-white").text("Abundante");
                    break;
                case "Normal":
                default:
                    $badge.addClass("bg-secondary text-white").text("Normal");
                    break;
            }

            $estadoTd.append($badge);
            $tr.append($estadoTd);


            // 4) Columna de “Acciones”: botones de Editar/Eliminar
            if (userType !== "ventas") {
                const $accionesTd = $("<td>").addClass("d-flex text-wrap");
                // Botón editar
                const $btnEditar = $('<button class="btn btn-primary me-1 btn-editar" data-bs-toggle="modal" data-bs-target="#modProductModal">Editar</button>');
                // Botón eliminar
                const $btnEliminar = $('<button class="btn btn-danger btn-eliminar">Eliminar</button>');

                $accionesTd.append($btnEditar, $btnEliminar);
                $tr.append($accionesTd);
            }

            // Le asignamos el _id
            $tr.attr("data-id", prod._id); 

            // 5) Agregamos la fila al <tbody>
            $tbody.append($tr);
        })    
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener productos:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}

function getProductoByID(productId){
    const dbChoice = $('#databaseSwitch:checked').val();
    $.get('/ferreteria/PHP/controladores/productos/getProductos.php', { db_choice: dbChoice, action:'getProductById', productId:productId }, function() {
    })
    .done(function(response){
        console.log("Productos recibidos:", response.producto);
        console.log("Usando:", response.db);
        // Asignar los valores a los inputs del formulario
        $("#editProductName").val(response.producto.productName);
        $("#editSupplierName").val(response.producto.supplierName);
        $("#editCategory").val(response.producto.category);
        $("#editStock").val(response.producto.stock);
        $("#editPrice").val(response.producto.price);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener productos:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}