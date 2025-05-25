$(document).ready(function () {
    $("#modProductForm").on("submit", function(e) {
        e.preventDefault(); // Previene el comportamiento por defecto (envío y recarga)
        
        // Aquí puedes acceder a los inputs, enviar AJAX, validar, etc.
        const productId    = $("#editId").val().trim();
        const productName  = $("#editProductName").val().trim();
        const category     = $("#editCategory").val().trim();
        const supplierName = $("#editSupplierName").val().trim();
        const stock        = $("#editStock").val().trim();
        const price        = $("#editPrice").val().trim();

        console.log("Editando el producto:", productId, productName, category, supplierName, stock, price);

        modProduct(productId, productName, category, supplierName, stock, price);
    });
});

function modProduct(productId, productName, category, supplierName, stock, price) {
    // 2) Validaciones básicas (por ejemplo, asegurarse de que haya ID y nombre)
    if (!productId) {
        console.error("Falta el product_id para modificar.");
        return;
    }
    if (!productName) {
        console.error("El nombre del producto no puede estar vacío.");
        return;
    }

    // 3) Construir los datos a enviar por PUT
    //    jQuery enviará esto en el cuerpo como application/x-www-form-urlencoded
    const datos = {
        product_id:    productId,
        productName:   productName,
        category:      category,
        supplierName:  supplierName,
        stock:         stock,
        price:         price
    };

    // 4) Petición AJAX tipo PUT al endpoint de actualización
    $.ajax({
        url: '/ferreteria/PHP/controladores/productos/modProductos.php',
        type: 'PUT',
        data: datos,
        success: function(response) {
            // 5) Manejar la respuesta del backend
            //    Se espera un JSON como { status: "success", mensaje_local: "...", mensaje_remoto: "..." }
            if (response.status === "success") {
                console.log("Modificación exitosa:", response.mensaje_local, response.mensaje_remoto);
                // Aquí puedes:
                // - Cerrar el modal manualmente:
                $('#modProductModal').modal('hide');
                // - Refrescar la tabla de productos:
                getProductos();
                // - Mostrar una alerta o notificación al usuario
            } else {
                console.error("Error al modificar:", response.mensaje_local, response.mensaje_remoto);
                // Mostrar mensaje de error al usuario (por ejemplo, en un div dentro del modal)
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}