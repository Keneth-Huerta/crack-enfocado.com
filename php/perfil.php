<?php
// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    // Si no está autenticado, redirigir al inicio de sesión
    header("Location: ../index.html");
    exit();
}

// Si el usuario está autenticado, mostrar el perfil
$usuario = $_SESSION['usuario'];  // Obtener el correo del usuario desde la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>
<body>
    <div class="perfil-container">
        <h1>Bienvenido, <?php echo $usuario; ?>!</h1>
        <p>Este es tu perfil.</p>

        <!-- Aquí puedes agregar más detalles del perfil, como nombre, boleta, etc. -->
        <a href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
