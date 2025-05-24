$(document).ready(function() {
    // Escucha el evento 'submit' del formulario con id 'loginForm'
    $('#loginForm').on('submit',login);

});

function login(event) {
    event.preventDefault(); // Evita el envío normal del formulario

    const sub = $("#submit");

    var form = event.target; // Captura el formulario desde el evento
    var formData = $(form).serialize(); // Serializa todos los campos del formulario

    var $messageDiv = $("#loginMessage"); // Referencia al div de mensajes

    // Limpia y oculta mensajes anteriores
    $messageDiv.removeClass("success error").hide().text("");
    sub.removeClass("btn-primary");                  // Quita estilo primario
    sub.addClass("disabled btn-secondary");    

    // Realiza la petición AJAX
    $.ajax({
        type: "POST",
        url: "PHP/login.php",
        data: formData,
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                $messageDiv.addClass("success").text(response.message).removeClass("d-none").show();
                setTimeout(function () {
                    window.location.href = "dashboard.php";
                }, 1500);
            } else {
                $messageDiv.addClass("error").text(response.message).removeClass("d-none").show();
                sub.removeClass("disabled btn-secondary");       // Quita estilo deshabilitado
                sub.addClass("btn-primary");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            sub.removeClass("disabled btn-secondary");       // Quita estilo deshabilitado
            sub.addClass("btn-primary");    
            console.error("Error AJAX:", textStatus, errorThrown);
            $messageDiv
                .addClass("error")
                .text("Error de conexión con el servidor. Inténtalo de nuevo.")
                .removeClass("d-none").show();
        },
    });
}