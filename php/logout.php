<?php
// Iniciar la sesión
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al inicio de sesión
header("Location: ../index.php");
exit();
?>
