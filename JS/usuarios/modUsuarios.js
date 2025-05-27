$(document).ready(function () {
    $('#modUserForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            _id: $('#editId').val(),
            username: $('#editUsername').val(),
            email: $('#editEmail').val(),
            userType: $('#editUserType').val(),
            password: $('#editPassword').val()
        };
        let dbChoice = $('input[name="databaseType"]:checked').val();
        if (!dbChoice) dbChoice = 'local';
        data.db_choice = dbChoice;
        $.ajax({
            url: '/ferreteria/PHP/controladores/usuarios/modUsuarios.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                $('#modUserModal').modal('hide');
                getUsuarios();
            },
            error: function () {
                alert('Error al modificar usuario');
            }
        });
    });
});
