$(document).ready(function () {
    getProveedores(); // Primer llamado para la tabla

    $('input[name="databaseType"]').on("change", function () {
        getProveedores(); // Recarga la tabla según la base seleccionada
    });

    $(document).on("click", ".btn-editar", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");

        // Asignar el id al input oculto
        $("#editId").val(id);
        const supplierId = id;
        console.log("ID del proveedor a editar:", supplierId);
        getProveedorByID(supplierId);
    });

    $(document).on("click", ".btn-eliminar", function () {
        const supplierId = $(this).closest("tr").data("id");
        console.log("ID del proveedor a eliminar:", supplierId);
        // Aquí puedes llamar a tu función de eliminar
    });
});

function getProveedores(){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/proveedores/getProveedores.php', { db_choice: dbChoice, action:'getSuppliers' }, function() {
    })
    .done(function(response){
        console.log("Proveedores recibidos:", response.proveedores);
        console.log("Usando:", response.db);

        // 1) Seleccionamos el <tbody> y lo vaciamos
        const $tbody = $("table.table tbody");
        $tbody.empty();

        // 2) Recorremos el array de proveedores
        response.proveedores.forEach(function(sup) {
            // Creamos fila <tr>
            const $tr = $("<tr>");

            // 3) Rellenamos cada <td> con el campo correspondiente
            $tr.append($("<td>").text(sup.supplierName));
            $tr.append($("<td>").text(sup.contactName || ""));
            $tr.append($("<td>").addClass("text-nowrap").text(sup.phone));
            $tr.append($("<td>").addClass("text-nowrap").text(sup.email));
            $tr.append($("<td>").text(sup.address));

            // 4) Columna de “Acciones”: botones de Editar/Eliminar
            const $accionesTd = $("<td>").addClass("d-flex text-wrap");
            // Botón editar
            const $btnEditar = $('<button class="btn btn-primary me-1 btn-editar" data-bs-toggle="modal" data-bs-target="#modSupplierModal">Editar</button>');
            // Botón eliminar
            const $btnEliminar = $('<button class="btn btn-danger btn-eliminar">Eliminar</button>');

            $accionesTd.append($btnEditar, $btnEliminar);
            $tr.append($accionesTd);

            // Le asignamos el _id
            $tr.attr("data-id", sup._id); 

            // 5) Agregamos la fila al <tbody>
            $tbody.append($tr);
        })    
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener proveedores:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}

function getProveedorByID(supplierId){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/proveedores/getProveedores.php', { db_choice: dbChoice, action:'getSupplierById', supplierId: supplierId }, function() {
    })
    .done(function(response){
        console.log("Proveedor recibido:", response.proveedor);
        console.log("Usando:", response.db);
        // Asignar los valores a los inputs del formulario
        $("#editSupplierName").val(response.proveedor.supplierName);
        $("#editContactName").val(response.proveedor.contactName || "");
        $("#editPhone").val(response.proveedor.phone);
        $("#editEmail").val(response.proveedor.email);
        $("#editAddress").val(response.proveedor.address);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener proveedor:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}
