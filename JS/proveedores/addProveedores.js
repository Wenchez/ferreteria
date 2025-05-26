$(document).ready(function () {
    $('#addSupplierForm').on('submit', function (e) {
        e.preventDefault();
        const data = {
            supplierName: $('#supplierName').val(),
            contactName: $('#contactName').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            address: $('#address').val()
        };
        $.ajax({
            url: '/ferreteria/PHP/controladores/proveedores/addProveedores.php',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (res) {
                $('#addSupplierModal').modal('hide');
                $('#addSupplierForm')[0].reset();
                getProveedores();
            },
            error: function () {
                alert('Error al añadir proveedor');
            }
        });
    });
});