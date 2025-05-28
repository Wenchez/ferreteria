$(document).ready(function () {
    $("#add_Product").on("click", function () {
        $("#addProductForm")[0].reset(); // Limpia todos los campos del formulario
    });

    $("#addProductForm").on("submit", function(e) {
        e.preventDefault(); // Previene el comportamiento por defecto (envío y recarga)
        
        const productName  = $("#productName").val().trim();
        const category     = $("#category").val().trim();
        const supplierName = $("#supplierName").val().trim();
        const stock        = $("#stock").val().trim();
        const price        = $("#price").val().trim();
        const dbChoice     = $('#databaseSwitch').val();

        console.log("Agregando el producto:", productName, category, supplierName, stock, price, dbChoice);

        addProduct(productName, category, supplierName, stock, price, dbChoice);
    });
});

function addProduct(productName, category, supplierName, stock, price, dbChoice){
    if (!productName) {
        console.error("El nombre del producto no puede estar vacío.");
        return;
    }

    const datos = {
        productName:   productName,
        category:      category,
        supplierName:  supplierName,
        stock:         stock,
        price:         price,
        db_choice:     dbChoice
    };

    $.ajax({
        url: '/ferreteria/PHP/controladores/productos/addProductos.php',
        type: 'POST',
        data: datos,
        success: function(response) {
            if (response.status === "success") {
                console.log("Creacion exitosa:", response.message, response.message);
                $('#addProductModal').modal('hide');
                getProductos();
                // - Mostrar una alerta o notificación al usuario
            } else {
                console.error("Error al agregar:", response.message, response.message);
                // Mostrar mensaje de error al usuario (por ejemplo, en un div dentro del modal)
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}