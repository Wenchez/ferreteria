$(document).ready(function () {
    $('#modClientForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            _id: $('#editId').val(),
            clientName: $('#editClientName').val(),
            phone: $('#editPhone').val(),
            email: $('#editEmail').val(),
            address: $('#editAddress').val()
        };
        const dbChoice = $('input[name="databaseType"]:checked').val();
        data.db_choice = dbChoice;
        $.ajax({
            url: '/ferreteria/PHP/controladores/clientes/modClientes.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                $('#modClientModal').modal('hide');
                getClientes();
            },
            error: function () {
                alert('Error al modificar cliente');
            }
        });
    });
});
