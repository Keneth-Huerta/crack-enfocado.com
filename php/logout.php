
<?php
// Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al inicio de sesión
header("Location: ../secion.php");
exit();
?>
