<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.html");
    exit();
}

// Configuración de la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

// Conexión a la base de datos
$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el correo del usuario desde la sesión
$correo = $_SESSION['usuario'];

// Preparar la consulta SQL para obtener los datos del usuario
$query = "SELECT nombre, apellido, boleta, carrera, semestre, foto_perfil, foto_portada, informacion_extra FROM registro WHERE correo = ?";
if ($stmt = mysqli_prepare($enlace, $query)) {
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $apellido, $boleta, $carrera, $semestre, $fotoPerfil, $fotoPortada, $informacionExtra);
    
    // Si el usuario existe, obtener los datos
    if (mysqli_stmt_fetch($stmt)) {
        // Datos del usuario
    } else {
        // Si no se encuentra el usuario en la base de datos
        echo "Usuario no encontrado.";
        exit();
    }

    // Cerrar la consulta
    mysqli_stmt_close($stmt);
}

// Cerrar la conexión a la base de datos
mysqli_close($enlace);
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
    <header>
        <h1>Bienvenido al Perfil de Usuario - IPN</h1>
    </header>

    <div class="perfil-container">
        <!-- Imagen de portada -->
        <div class="perfil-portada">
            <img src="<?php echo $fotoPortada ? $fotoPortada : 'img/portada.jpg'; ?>" alt="Imagen de portada">
        </div>

        <!-- Contenedor de la información del usuario -->
        <div class="perfil-info">
            <div class="perfil-img-container">
                <img src="<?php echo $fotoPerfil ? $fotoPerfil : 'img/avatar.png'; ?>" alt="Avatar" class="perfil-img">
            </div>
            <div class="perfil-details">
                <h1><?php echo $nombre . ' ' . $apellido; ?></h1>
                <p><strong>Boleta:</strong> <?php echo $boleta; ?></p>
                <p><strong>Carrera:</strong> <?php echo $carrera; ?></p>
                <p><strong>Semestre:</strong> <?php echo $semestre; ?></p>
                <p><strong>Acerca de mí:</strong> <?php echo nl2br(htmlspecialchars($informacionExtra)); ?></p>
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
