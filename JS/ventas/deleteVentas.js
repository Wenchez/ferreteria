$(document).ready(function () {
    $(document).on("click", ".btn-eliminar", function () {
        const saleId = $(this).closest("tr").data("id");

        if (!saleId) {
            console.error("ID no válido para eliminar.");
            return;
        }else{
            console.log(saleId);
            deleteVenta(saleId);
        }
    });
});

function deleteVenta(saleId){
    $.ajax({
        url: '/ferreteria/PHP/controladores/ventas/deleteVentas.php',
        type: 'POST',
        data: { id: saleId },
        success: function (response) {
            if (response.status === "success") {
                console.log("Venta eliminada:", response.mensaje_local);
                getventas(); // Recargar la tabla
            } else {
                console.error("Error al eliminar:", response.mensaje_local, response.mensaje_remoto);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}