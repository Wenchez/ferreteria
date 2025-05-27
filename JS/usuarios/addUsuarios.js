$(document).ready(function () {
    $('#addUserForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            username: $('#username').val(),
            email: $('#email').val(),
            userType: $('#userType').val(),
            password: $('#password').val()
        };
        let dbChoice = $('input[name="databaseType"]:checked').val();
        if (!dbChoice) dbChoice = 'local';
        data.db_choice = dbChoice;
        $.ajax({
            url: '/ferreteria/PHP/controladores/usuarios/addUsuarios.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                $('#addUserModal').modal('hide');
                $('#addUserForm')[0].reset();
                getUsuarios();
            },
            error: function () {
                alert('Error al añadir usuario');
            }
        });
    });
});
