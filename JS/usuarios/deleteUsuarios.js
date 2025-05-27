function deleteUsuario(id) {
    let dbChoice = $('input[name="databaseType"]:checked').val();
    if (!dbChoice) dbChoice = 'local';
    if (confirm('¿Seguro que deseas eliminar este usuario?')) {
        $.ajax({
            url: '/ferreteria/PHP/controladores/usuarios/deleteUsuarios.php',
            type: 'POST',
            data: JSON.stringify({ _id: id, db_choice: dbChoice }),
            contentType: 'application/json',
            success: function () {
                getUsuarios();
            },
            error: function () {
                alert('Error al eliminar usuario');
            }
        });
    }
}

$(document).on('click', '.btn-eliminar', function () {
    const userId = $(this).closest('tr').data('id');
    deleteUsuario(userId);
});
