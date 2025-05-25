$(document).ready(function () {
    updateConnectionStatus();

    $('input[name="databaseType"]').on("change", function () {
        updateConnectionStatus();
        getProductos();
    });
});

function updateConnectionStatus() {
    const statusElement = $("#connectionStatus");
    const localDbRadio = $("#localDb");
    const remoteDbRadio = $("#remoteDb");

    if (localDbRadio.is(":checked")) {
        statusElement.html(
        '<i class="bi bi-check-circle-fill me-1"></i> Usando almacenamiento local del navegador'
        );
        statusElement
        .removeClass("text-warning text-danger")
        .addClass("text-success"); // Asegura color verde
    } else if (remoteDbRadio.is(":checked")) {
        // Aquí puedes personalizar el mensaje y el color para la base de datos remota
        statusElement.html(
        '<i class="bi bi-check-circle-fill me-1"></i> Usando almacenamiento remoto (MongoDB Atlas)'
        );
        statusElement
        .removeClass("text-success text-danger")
        .addClass("text-primary"); // Por ejemplo, color azul para remoto
    }
}
