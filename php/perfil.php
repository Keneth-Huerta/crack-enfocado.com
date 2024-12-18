<?php
session_start();
require_once 'conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener la información del usuario desde la base de datos
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT * FROM perfiles WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/misestilos.css">
</head>
<body>
    <div class="perfil-container">
        <!-- Foto de portada -->
        <div class="foto-portada">
            <img src="<?php echo htmlspecialchars($perfil['foto_portada'] ?? '../media/default-cover.jpg'); ?>" alt="Foto de portada">
        </div>

        <h1 class="titulo-perfil">Bienvenido, <?php echo htmlspecialchars($perfil['nombre']); ?></h1>

        <div class="perfil-info">
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($perfil['foto_perfil'] ?? '../media/user.png'); ?>" alt="Foto de perfil">
            </div>
            <div class="informacion">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($perfil['nombre']); ?></p>
                <p><strong>Apellido:</strong> <?php echo htmlspecialchars($perfil['apellido']); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($perfil['carrera']); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($perfil['semestre']); ?></p>
                <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($perfil['informacion_extra'])); ?></p>
            </div>
        </div>

        <div class="acciones">
            <a href="editar_perfil.php">Editar perfil</a>
            <a href="logout.php" class="btn-cerrar-sesion">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
