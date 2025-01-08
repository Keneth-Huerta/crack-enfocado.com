<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['usuario_id'])) {
    // Si está logueado, redirigir al inicio o página principal
    header("Location: php/Principal.php"); // O la URL donde se encuentra el contenido principal
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/media/logoweb.svg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="form-container">
        <h1>Iniciar sesión</h1>
        <form action="php/login.php" method="POST">
            <div class="form-group">
                <input type="text" name="login_input" placeholder="Nombre de Usuario o Correo electrónico" required>
            </div>

            <div class="form-group">
                <input type="password" name="contra" placeholder="Contraseña" required>
            </div>

            <button type="submit" name="iniciar_sesion">Iniciar sesión</button>

            <div class="login-link">
                <a href="crearCuenta.html">¿No tienes cuenta? Regístrate</a>
            </div>
        </form>
    </div>

    <!-- Scripts necesarios -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.validate/1.19.3/jquery.validate.min.js"></script>
</body>

</html>