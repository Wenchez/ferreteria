$(document).ready(function () {
    getUsuarios();

    $('#databaseSwitch').on("change", function () {
        setTimeout(function() {
            getUsuarios();
        }, 50);
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
    const dbChoice = $('#databaseSwitch').val();
    console.log(dbChoice)
    $.ajax({
        url: '/ferreteria/PHP/controladores/usuarios/getUsuarios.php',
        type: 'GET',
        contentType: 'application/json',
        data: {
            action: 'getUsers',
            db_choice: dbChoice
        },
        dataType: 'json',
        success: function(response) {
            const $tbody = $("table.table tbody");
            $tbody.empty();
            response.usuarios.forEach(function(user) {
                const $tr = $("<tr>");
                $tr.append($("<td>").text(user.username));
                $tr.append($("<td>").addClass("text-nowrap").text(user.email));
                // Crear celda del estado
                const $tipo = $("<td>");

                // Crear el badge
                const $badge = $("<span>").addClass("text-nowrap badge fs-6 fw-bold");
                switch (user.userType) {
                    case "ventas":
                        $badge.addClass("bg-success text-white").text("Ventas");
                        break;
                    case "admin":
                        $badge.addClass("bg-primary text-white").text("Administrador");
                        break;
                    default:
                        console.log("Ninguno")
                        $badge.addClass("bg-secondary text-white").text("");
                        break;
                }

                $tipo.append($badge);
                $tr.append($tipo);

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
    const dbChoice = $('#databaseSwitch').val();
    $.get('/ferreteria/PHP/controladores/usuarios/getUsuarios.php', { db_choice: dbChoice, action:'getUserById', userId: userId }, function() {})
    .done(function(response){
        console.log(response)
        $("#editUserName").val(response.usuario.username);
        $("#editEmail").val(response.usuario.email);
        $("#editRole").val(response.usuario.userType);
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error al obtener usuario:", textStatus, errorThrown);
        console.error("Respuesta del servidor:", jqXHR.responseText);
    });
}
