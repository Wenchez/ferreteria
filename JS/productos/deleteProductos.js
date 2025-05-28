$(document).ready(function () {
    $(document).on("click", ".btn-eliminar", function () {
        const productId = $(this).closest("tr").data("id");

        if (!productId) {
            console.error("ID no válido para eliminar.");
            return;
        }else{
            console.log(productId);
            deleteProducto(productId);
        }
    });
});

function deleteProducto(productId){
    const dbChoice     = $('#databaseSwitch').val();
    $.ajax({
        url: '/ferreteria/PHP/controladores/productos/deleteProductos.php',
        type: 'POST',
        data: { id: productId, db_choice: dbChoice },
        success: function (response) {
            if (response.status === "success") {
                console.log("Producto eliminado:", response.message);
                getProductos(); // Recargar la tabla
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