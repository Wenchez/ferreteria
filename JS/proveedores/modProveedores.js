$(document).ready(function () {
    $('#modSupplierForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            _id: $('#editId').val(),
            supplierName: $('#editSupplierName').val(),
            contactName: $('#editContactName').val(),
            phone: $('#editPhone').val(),
            email: $('#editEmail').val(),
            address: $('#editAddress').val()
        };
        $.ajax({
            url: '/ferreteria/PHP/controladores/proveedores/modProveedores.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                $('#modSupplierModal').modal('hide');
                getProveedores();
            },
            error: function () {
                alert('Error al modificar proveedor');
            }
        });
    });
});
