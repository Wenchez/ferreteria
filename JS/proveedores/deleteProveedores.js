function deleteSupplier(id) {
    if (confirm('¿Seguro que deseas eliminar este proveedor?')) {
        $.ajax({
            url: '/ferreteria/PHP/controladores/proveedores/deleteProveedores.php',
            type: 'POST',
            data: JSON.stringify({ _id: id }),
            contentType: 'application/json',
            success: function () {
                getProveedores();
            },
            error: function () {
                alert('Error al eliminar proveedor');
            }
        });
    }
}

$(document).on('click', '.btn-eliminar', function () {
    const supplierId = $(this).closest('tr').data('id');
    deleteSupplier(supplierId);
});
