$(document).ready(function () {
    getUsuarios();

    $('input[name="databaseType"]').on("change", function () {
        getUsuarios();
    });

    $(document).on("click", ".btn-editar", function () {
        const $fila = $(this).closest("tr");
        const id = $fila.data("id");
        $("#editId").val(id);
        getUsuarioByID(id);
    });

    $(document).on("click", ".btn-eliminar", function () {
        const userId = $(this).closest("tr").data("id");
        deleteUsuario(userId);
    });
});

function getUsuarios(){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.ajax({
        url: '/ferreteria/PHP/controladores/usuarios/getUsuarios.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ db_choice: dbChoice }),
        dataType: 'json',
        success: function(response) {
            const $tbody = $("table.table tbody");
            $tbody.empty();
            response.usuarios.forEach(function(user) {
                const $tr = $("<tr>");
                $tr.append($("<td>").text(user.username));
                $tr.append($("<td>").addClass("text-nowrap").text(user.email));
                $tr.append($("<td>").addClass("text-nowrap").text(user.userType || user.role || ''));
                $tr.append($("<td>").addClass("text-nowrap").text(user.password));
                // Mostrar origen si viene de ambas
                if(dbChoice === 'both') {
                    $tr.append($("<td>").addClass("text-nowrap").html('<span class="badge bg-info">' + (user.db_origin || '') + '</span>'));
                }
                const $accionesTd = $("<td>").addClass("d-flex text-wrap");
                const $btnEditar = $('<button class="btn btn-primary me-1 btn-editar" data-bs-toggle="modal" data-bs-target="#modUserModal">Editar</button>');
                const $btnEliminar = $('<button class="btn btn-danger btn-eliminar">Eliminar</button>');
                $accionesTd.append($btnEditar, $btnEliminar);
                $tr.append($accionesTd);
                $tr.attr("data-id", user._id);
                $tbody.append($tr);
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error al obtener usuarios:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}

function getUsuarioByID(userId){
    const dbChoice = $('input[name="databaseType"]:checked').val();
    $.get('/ferreteria/PHP/controladores/usuarios/getUsuarios.php', { db_choice: dbChoice, action:'getUserById', userId: userId }, function() {})
    .done(function(response){
        $("#editUsername").val(response.usuario.username);
        $("#editEmail").val(response.usuario.email);
        $("#editUserType").val(response.usuario.userType);
        $("#editPassword").val(response.usuario.password);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener usuario:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}
