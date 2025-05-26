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
    $.ajax({
        url: '/ferreteria/PHP/controladores/productos/deleteProductos.php',
        type: 'POST',
        data: { id: productId },
        success: function (response) {
            if (response.status === "success") {
                console.log("Producto eliminado:", response.mensaje_local);
                getProductos(); // Recargar la tabla
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