<?php
// Esto es practicamente una clase universal por lo que no era necesario ponerla dentro de los controladores de adminArea ni ciudadano
session_start();
session_unset();
session_destroy();
header("Location: ../login.html");
exit;
?>