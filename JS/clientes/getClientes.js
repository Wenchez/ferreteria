$(document).ready(function () {
    getClientes();

    $('input[name="databaseType"]').on("change", function () {
        getClientes();
    });

    $(document).on("click", ".btn-editar", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");
        $("#editId").val(id);
        getClienteByID(id);
    });

    $(document).on("click", ".btn-eliminar", function () {
        const clientId = $(this).closest("tr").data("id");
        deleteCliente(clientId);
    });
});

function getClientes(){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/clientes/getClientes.php', { db_choice: dbChoice, action:'getClients' }, function() {})
    .done(function(response){
        const $tbody = $("table.table tbody");
        $tbody.empty();
        response.clientes.forEach(function(cli) {
            const $tr = $("<tr>");
            $tr.append($("<td>").text(cli.clientName));
            $tr.append($("<td>").addClass("text-nowrap").text(cli.phone));
            $tr.append($("<td>").addClass("text-nowrap").text(cli.email));
            $tr.append($("<td>").text(cli.address));
            const $accionesTd = $("<td>").addClass("d-flex text-wrap");
            const $btnEditar = $('<button class="btn btn-primary me-1 btn-editar" data-bs-toggle="modal" data-bs-target="#modClientModal">Editar</button>');
            const $btnEliminar = $('<button class="btn btn-danger btn-eliminar">Eliminar</button>');
            $accionesTd.append($btnEditar, $btnEliminar);
            $tr.append($accionesTd);
            $tr.attr("data-id", cli._id);
            $tbody.append($tr);
        });
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener clientes:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}

function getClienteByID(clientId){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/clientes/getClientes.php', { db_choice: dbChoice, action:'getClientById', clientId: clientId }, function() {})
    .done(function(response){
        $("#editClientName").val(response.cliente.clientName);
        $("#editPhone").val(response.cliente.phone);
        $("#editEmail").val(response.cliente.email);
        $("#editAddress").val(response.cliente.address);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener cliente:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}
