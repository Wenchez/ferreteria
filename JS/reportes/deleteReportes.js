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
    dbChoice = $('#databaseSwitch').val();
    $.ajax({
        url: '/ferreteria/PHP/controladores/ventas/deleteVentas.php',
        type: 'POST',
        data: { id: saleId, db_choice: dbChoice },
        success: function (response) {
            if (response.status === "success") {
                console.log("Venta eliminada:", response.message);
                getReportes(); // Recargar la tabla
            } else {
                console.error("Error al eliminar:", response.message, response.message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}