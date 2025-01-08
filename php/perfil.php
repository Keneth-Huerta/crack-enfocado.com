<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// Verificar conexión
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario desde la URL
if (isset($_GET['usuario_id'])) {
    $usuario_ids = (int) $_GET['usuario_id'];
} else {
    $usuario_ids = $_SESSION['usuario_id'];
}
$perfil = [];
$publicaciones = [];
$ventas = [];

// Obtener publicaciones del usuario con manejo de errores
try {
    $publicaciones_query = "SELECT id_publicacion, contenido, imagen, fecha_publicada, usuario_id 
                            FROM publicaciones 
                            WHERE usuario_id = ? 
                            ORDER BY fecha_publicada DESC";

    if ($stmt_publicaciones = mysqli_prepare($enlace, $publicaciones_query)) {
        mysqli_stmt_bind_param($stmt_publicaciones, "i", $usuario_ids);
        mysqli_stmt_execute($stmt_publicaciones);
        $publicaciones_result = mysqli_stmt_get_result($stmt_publicaciones);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Obtener ventas del usuario con manejo de errores
try {
    $ventas_query = "SELECT id, producto, precio, descripcion, imagen 
                     FROM productos 
                     WHERE usuario_id = ? 
                     ORDER BY id DESC";
    if ($stmt_ventas = mysqli_prepare($enlace, $ventas_query)) {
        mysqli_stmt_bind_param($stmt_ventas, "i", $usuario_ids);
        mysqli_stmt_execute($stmt_ventas);
        $ventas_result = mysqli_stmt_get_result($stmt_ventas);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Obtener perfil con manejo de errores mejorado
try {
    $query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $usuario_ids);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $perfil = mysqli_fetch_assoc($resultado);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Valores predeterminados seguros
$username = $perfil['username'] ?? 'Usuario no disponible';
$nombre = $perfil['nombre'] ?? 'Nombre no disponible';
$apellido = $perfil['apellido'] ?? 'Apellido no disponible';
$carrera = $perfil['carrera'] ?? 'Carrera no disponible';
$semestre = $perfil['semestre'] ?? 'Semestre no disponible';
$informacion_extra = $perfil['informacion_extra'] ?? 'No disponible';
$foto_perfils = $perfil['foto_perfil'] ?? '../media/user.png';
$foto_portada = $perfil['foto_portada'] ?? '../media/user_icon_001.jpg';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="../css/misestilos.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="perfil-container">
        <!-- Foto de portada -->
        <div class="foto-portada">
            <img src="<?php echo htmlspecialchars($foto_portada); ?>" alt="Foto de portada">
        </div>

        <h1 class="titulo-perfil"><?php echo htmlspecialchars($username); ?></h1>

        <div class="perfil-info">
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($foto_perfils); ?>" alt="Foto de perfil">
            </div>
            <div class="informacion">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($carrera); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($semestre); ?></p>
                <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($informacion_extra)); ?></p>
            </div>
        </div>

        <!-- Botones para el dueño del perfil -->
        <?php if ($usuario_ids == $_SESSION['usuario_id']): ?>
            <div class="acciones">
                <a href="editar_perfil.php" class="btn-editar"><i class="fas fa-edit"></i> Editar perfil</a>
                <a href="logout.php" class="btn-cerrar-sesion"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        <?php endif; ?>

        <!-- Publicaciones -->
        <div class="publicaciones-usuario">
            <h2>Publicaciones</h2>
            <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                <div class="lista-publicaciones">
                    <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
                        <div class="publicacion-item">
                            <p class="fecha">
                                <?php echo (new DateTime($publicacion['fecha_publicada']))->format('d/m/Y H:i'); ?>
                            </p>
                            <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                            <?php if (!empty($publicacion['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de publicación">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No hay publicaciones disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Sección de ventas -->
        <div class="ventas-usuario">
            <h2>Ventas</h2>
            <?php if ($ventas_result && mysqli_num_rows($ventas_result) > 0): ?>
                <div class="lista-ventas">
                    <?php while ($venta = mysqli_fetch_assoc($ventas_result)): ?>
                        <div class="venta-item">
                            <h4><?php echo htmlspecialchars($venta['producto']); ?></h4>
                            <p><strong>Precio:</strong> $<?php echo htmlspecialchars($venta['precio']); ?></p>
                            <p><?php echo htmlspecialchars($venta['descripcion']); ?></p>
                            <?php if (!empty($venta['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($venta['imagen']); ?>" alt="Imagen del producto">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No hay ventas disponibles.</p>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Cerrar conexiones de manera segura
if (isset($stmt_publicaciones)) mysqli_stmt_close($stmt_publicaciones);
if (isset($stmt_ventas)) mysqli_stmt_close($stmt_ventas);
if (isset($stmt)) mysqli_stmt_close($stmt);
mysqli_close($enlace);
?>