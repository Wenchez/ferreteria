function deleteCliente(id) {
    const dbChoice = $('#databaseSwitch:checked').val();
    if (confirm('¿Seguro que deseas eliminar este cliente?')) {
        $.ajax({
            url: '/ferreteria/PHP/controladores/clientes/deleteClientes.php',
            type: 'POST',
            data: JSON.stringify({ _id: id, db_choice: dbChoice }),
            contentType: 'application/json',
            success: function () {
                getClientes();
            },
            error: function () {
                alert('Error al eliminar cliente');
            }
        });
    }
}

$(document).on('click', '.btn-eliminar', function () {
    const clientId = $(this).closest('tr').data('id');
    deleteCliente(clientId);
});
