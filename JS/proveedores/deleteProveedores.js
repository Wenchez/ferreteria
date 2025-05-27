function deleteSupplier(userId) {
    if (confirm('¿Seguro que deseas eliminar este proveedor?')) {
       const dbChoice = $('#databaseSwitch').is(':checked') ? 'remote' : 'local';

        $.ajax({
            url: '/ferreteria/PHP/controladores/proveedores/deleteProveedores.php',
            type: 'POST',
            data: JSON.stringify({ _id: userId, db_choice: dbChoice }),
            contentType: 'application/json',
            success: function () {
                getProveedores();
            },
            error: function (xhr) {
                alert('Error al eliminar usuario');
                console.error(xhr.responseText);
            }
        });
        }
}

$(document).on('click', '.btn-eliminar', function () {
    const supplierId = $(this).closest('tr').data('id');
    deleteSupplier(supplierId);
});
