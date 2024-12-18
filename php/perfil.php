<?php
// Asegúrate de que la sesión esté activa
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.html");
    exit();
}

// Obtener los datos del usuario desde la base de datos (esto es solo un ejemplo)
$usuario = $_SESSION['usuario'];
// Ejemplo de datos adicionales, en un escenario real los obtendrás de la base de datos
$nombreCompleto = "Juan Pérez";
$boleta = "2019-12345";
$carrera = "Ingeniería en Sistemas Computacionales";
$semestre = "4° Semestre";
$fotoPerfil = "img/avatar.png";  // Foto de perfil predeterminada
$fotoPortada = "img/portada.jpg";  // Foto de portada predeterminada
$informacionExtra = "Apasionado por la programación y el desarrollo web. Actualmente participo en varios proyectos de investigación en la IPN.";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - IPN</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="perfil-container">
        <!-- Imagen de portada -->
        <div class="perfil-portada">
            <img src="<?php echo $fotoPortada; ?>" alt="Imagen de portada">
        </div>

        <!-- Contenedor de la información del usuario -->
        <div class="perfil-info">
            <div class="perfil-img-container">
                <img src="<?php echo $fotoPerfil; ?>" alt="Avatar" class="perfil-img">
            </div>
            <div class="perfil-details">
                <h1><?php echo $nombreCompleto; ?></h1>
                <p><strong>Boleta:</strong> <?php echo $boleta; ?></p>
                <p><strong>Carrera:</strong> <?php echo $carrera; ?></p>
                <p><strong>Semestre:</strong> <?php echo $semestre; ?></p>
                <p><strong>Acerca de mí:</strong> <?php echo $informacionExtra; ?></p>
            </div>
        </div>

        <!-- Acciones y botones -->
        <div class="perfil-actions">
            <a href="editar_perfil.php" class="btn">Editar perfil</a>
            <a href="logout.php" class="btn">Cerrar sesión</a>
        </div>

        <!-- Sección de Actividades o Proyectos -->
        <div class="perfil-actividades">
            <h2>Mis Actividades</h2>
            <ul>
                <li>Proyecto 1: Desarrollo de aplicación para gestión de inventarios</li>
                <li>Proyecto 2: Investigación sobre IA en programación de videojuegos</li>
                <li>Curso 1: Introducción a la Inteligencia Artificial</li>
            </ul>
        </div>
    </div>
</body>
</html>
