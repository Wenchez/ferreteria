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
        const dbChoice     = $('#databaseSwitch').val();

        console.log("Editando el producto:", productId, productName, category, supplierName, stock, price, dbChoice);

        modProduct(productId, productName, category, supplierName, stock, price, dbChoice);
    });
});

function modProduct(productId, productName, category, supplierName, stock, price, dbChoice) {
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
    const datos = {
        product_id:    productId,
        productName:   productName,
        category:      category,
        supplierName:  supplierName,
        stock:         stock,
        price:         price,
        db_choice:     dbChoice
    };

    // 4) Petición AJAX tipo PUT al endpoint de actualización
    $.ajax({
        url: '/ferreteria/PHP/controladores/productos/modProductos.php',
        type: 'PUT',
        data: datos,
        success: function(response) {
            // 5) Manejar la respuesta del backend
            if (response.status === "success") {
                console.log("Modificación exitosa:", response.message, response.message);
                $('#modProductModal').modal('hide');
                getProductos();
            } else {
                console.error("Error al modificar:", response.message, response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición AJAX:", textStatus, errorThrown);
            console.error("Respuesta del servidor:", jqXHR.responseText);
        }
    });
}