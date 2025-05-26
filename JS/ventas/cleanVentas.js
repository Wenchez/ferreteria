$(document).ready(function() {
    // Ejemplo: si quieres limpiar todo al hacer clic en un botón “Limpiar”
    $('#clean').on('click', function() {
        clearAllFields();
    });
});

function clearAllFields() {
    // 1) Limpiar el nombre del cliente
    $('#clientSearchInput').val('');
    
    // 2) Vaciar todas las filas de la tabla de items
    $('#saleItemsTableBody').empty();
    
    // 3) Resetear los resúmenes a $0.00
    $('#summarySubtotal').text('$0.00');
    $('#summaryIva').text('$0.00');
    $('#summaryTotal').text('$0.00');
    
    // 4) Desactivar el botón de procesar pago (asumiendo que existe updateButtonState)
    if (typeof updateButtonState === 'function') {
        updateButtonState();
    } else {
        // Si no hay updateButtonState, desactivarlo directamente:
        $('#processPaymentButton').prop('disabled', true);
    }
    updateSummary();
}