<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener la información del usuario desde la base de datos
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT * FROM perfiles WHERE usuario_id = ?";

try {
    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (!$resultado) {
        throw new Exception("Error al obtener los datos del perfil");
    }
    
    $perfil = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    // Verificar si los campos existen y asignarles valores predeterminados si no están definidos
    $nombre = $perfil['nombre'] ?? 'Nombre no disponible';
    $apellido = $perfil['apellido'] ?? 'Apellido no disponible';
    $carrera = $perfil['carrera'] ?? 'Carrera no disponible';
    $semestre = $perfil['semestre'] ?? 'Semestre no disponible';
    $informacion_extra = $perfil['informacion_extra'] ?? 'No disponible';
    $foto_perfil = $perfil['foto_perfil'] ?? '../media/user.png';
    $foto_portada = $perfil['foto_portada'] ?? '../media/default-cover.jpg';
} catch (Exception $e) {
    // Manejo de errores
    error_log($e->getMessage());
    $nombre = 'Usuario';
    $apellido = '';
    $carrera = 'No disponible';
    $semestre = 'No disponible';
    $informacion_extra = 'Error al cargar información';
    $foto_perfil = '../media/user.png';
    $foto_portada = '../media/default-cover.jpg';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="stylesheet" href="../css/misestilos.css">
</head>
<body>
    <?php include('header.php'); ?>
    
    <div class="perfil-container">
        <!-- Foto de portada -->
        <div class="foto-portada">
            <img src="<?php echo htmlspecialchars($foto_portada); ?>" alt="Foto de portada">
        </div>

        <h1 class="titulo-perfil">Bienvenido, <?php echo htmlspecialchars($nombre); ?></h1>

        <div class="perfil-info">
            <!-- Foto de perfil -->
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil">
            </div>
            
            <div class="informacion">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($carrera); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($semestre); ?></p>
                <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($informacion_extra)); ?></p>
            </div>
        </div>

        <div class="acciones">
            <a href="editar_perfil.php" class="btn-editar">Editar perfil</a>
            <a href="logout.php" class="btn-cerrar-sesion">Cerrar sesión</a>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>