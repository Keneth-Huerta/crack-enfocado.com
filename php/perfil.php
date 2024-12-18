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
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <div class="perfil-container">
        <div class="perfil-header">
            <div class="perfil-img-container">
                <!-- Imagen de perfil del usuario (puedes agregar una imagen por defecto o permitir que el usuario suba una) -->
                <img src="img/avatar.png" alt="Avatar" class="perfil-img">
            </div>
            <div class="perfil-info">
                <h1>Bienvenido, <span id="nombre-usuario"><?php echo $_SESSION['usuario']; ?></span>!</h1>
                <p><strong>Correo:</strong> <span id="correo-usuario"><?php echo $_SESSION['usuario']; ?></span></p>
                <!-- Puedes agregar más detalles como nombre completo, fecha de registro, etc. -->
            </div>
        </div>

        <div class="perfil-actions">
            <!-- Opciones para editar perfil, cambiar contraseña, etc. -->
            <a href="editar_perfil.php" class="btn">Editar perfil</a>
            <a href="logout.php" class="btn">Cerrar sesión</a>
        </div>
    </div>
</body>

</html>
