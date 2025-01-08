/**
 * This script handles the user logout process.
 * 
 * Steps performed:
 * 1. Starts the session if it is not already started.
 * 2. Unsets all session variables.
 * 3. Destroys the session.
 * 4. Redirects the user to the login page.
 * 
 */
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
