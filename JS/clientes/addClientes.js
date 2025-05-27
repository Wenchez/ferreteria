$(document).ready(function () {
    $('#addClientForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            clientName: $('#clientName').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            address: $('#address').val()
        };
        const dbChoice = $('#databaseSwitch').val();
        data.db_choice = dbChoice;
        $.ajax({
            url: '/ferreteria/PHP/controladores/clientes/addClientes.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                alert(JSON.stringify(res)); // Depuración: muestra la respuesta del backend
                $('#addClientModal').modal('hide');
                $('#addClientForm')[0].reset();
                getClientes();
            },
            error: function (xhr) {
                alert('Error al añadir cliente: ' + xhr.responseText);
            }
        });
    });
});
