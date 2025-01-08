/**
 * This script handles the session management and login page rendering for the CECyT 3 Academic Social Network.
 * 
 * If a user session is already active, it redirects the user to the main content page.
 * Otherwise, it displays the login form.
 * 
 * Meta tags are included for SEO and social media sharing purposes.
 * 
 * @file /d:/crack-enfocado.com/secion.php
 * 
 * @meta charset UTF-8
 * @meta description "Red Social Académica del CECyT 3 'Estanislao Ramírez Ruiz'. Conecta con compañeros, comparte materiales y conocimiento."
 * @meta keywords "CECyT 3, IPN, red social académica, estudiantes, materiales escolares"
 * 
 * @meta og:type website
 * @meta og:url https://steelblue-pelican-262083.hostingersite.com/
 * @meta og:title "CECyT 3 - Red Social Académica"
 * @meta og:description "Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento."
 * @meta og:image https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png
 * @meta og:image:alt "CECyT 3 Red Social Académica"
 * @meta og:site_name CECyT 3
 * @meta og:locale es_MX
 * 
 * @meta twitter:card summary_large_image
 * @meta twitter:url https://steelblue-pelican-262083.hostingersite.com/
 * @meta twitter:title "CECyT 3 - Red Social Académica"
 * @meta twitter:description "Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento."
 * @meta twitter:image https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png
 * 
 * @meta og:image:width 1200
 * @meta og:image:height 630
 * 
 * @meta apple-mobile-web-app-capable yes
 * @meta apple-mobile-web-app-status-bar-style black
 * @meta apple-mobile-web-app-title CECyT 3
 * 
 * @meta theme-color #741931
 * @link manifest /manifest.json
 * 
 * @link mask-icon /media/safari-pinned-tab.svg color="#741931"
 * @link shortcut icon /media/logoweb.svg type="image/x-icon"
 * @meta viewport width=device-width, initial-scale=1.0
 * 
 * @title Iniciar sesión
 * @link rel="stylesheet" href="css/login.css"
 * 
 * @script src="https://code.jquery.com/jquery-3.7.1.min.js"
 * @script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.validate/1.19.3/jquery.validate.min.js"
 * 
 * @form action="php/login.php" method="POST"
 * @input type="text" name="login_input" placeholder="Nombre de Usuario o Correo electrónico" required
 * @input type="password" name="contra" placeholder="Contraseña" required
 * @button type="submit" name="iniciar_sesion" Iniciar sesión
 * @link href="crearCuenta.html" ¿No tienes cuenta? Regístrate
 */
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
    <!-- Meta tags básicos -->
    <meta name="description" content="Red Social Académica del CECyT 3 'Estanislao Ramírez Ruiz'. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="keywords" content="CECyT 3, IPN, red social académica, estudiantes, materiales escolares">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta property="og:title" content="CECyT 3 - Red Social Académica">
    <meta property="og:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta property="og:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">
    <meta property="og:image:alt" content="CECyT 3 Red Social Académica">
    <meta property="og:site_name" content="CECyT 3">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta name="twitter:title" content="CECyT 3 - Red Social Académica">
    <meta name="twitter:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="twitter:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">

    <!-- WhatsApp -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="CECyT 3">

    <!-- Android -->
    <meta name="theme-color" content="#741931">
    <link rel="manifest" href="/manifest.json">

    <!-- Favicon y íconos -->
    <link rel="mask-icon" href="/media/safari-pinned-tab.svg" color="#741931">
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