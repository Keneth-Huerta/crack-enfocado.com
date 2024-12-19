<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// Verificar conexión
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar si se proporcionó el ID de la publicación
if (!isset($_GET['id_publicacion'])) {
    echo "Error: No se proporcionó un ID de publicación.";
    exit();
}

$id_publicacion = (int) $_GET['id_publicacion'];

// Consultar detalles de la publicación
$publicacion = [];
try {
    $query = "SELECT p.id_publicacion, p.contenido, p.imagen, p.fecha_publicada, u.username, 
                     u.nombre, u.apellido 
              FROM publicaciones p
              JOIN usuarios u ON p.usuario_id = u.id
              WHERE p.id_publicacion = ?";

    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id_publicacion);

        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            $publicacion = mysqli_fetch_assoc($resultado);

            if (!$publicacion) {
                echo "No se encontró la publicación.";
                exit();
            }
        } else {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_error($enlace));
        }
        mysqli_stmt_close($stmt);
    } else {
        throw new Exception("Error en la preparación de la consulta: " . mysqli_error($enlace));
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "Ocurrió un error al obtener los detalles de la publicación.";
    exit();
}

// Valores predeterminados para mostrar en caso de datos faltantes
$titulo = "Detalles de la publicación";
$username = $publicacion['username'] ?? 'Usuario desconocido';
$nombre_completo = ($publicacion['nombre'] ?? '') . ' ' . ($publicacion['apellido'] ?? '');
$contenido = $publicacion['contenido'] ?? 'Sin contenido.';
$imagen = $publicacion['imagen'] ?? '../media/user_icon_001.jpg';
$fecha_publicada = new DateTime($publicacion['fecha_publicada'] ?? 'now');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <link rel="stylesheet" href="../css/misestilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="detalle-publicacion-container">
        <h1 class="titulo-publicacion"><?php echo htmlspecialchars($titulo); ?></h1>

        <div class="detalle-publicacion">
            <!-- Información del usuario -->
            <div class="usuario-info">
                <p><strong>Publicado por:</strong> <?php echo htmlspecialchars($nombre_completo); ?> (<?php echo htmlspecialchars($username); ?>)</p>
                <p><strong>Fecha:</strong> <?php echo $fecha_publicada->format('d/m/Y H:i'); ?></p>
            </div>

            <!-- Contenido de la publicación -->
            <div class="contenido-publicacion">
                <p><?php echo nl2br(htmlspecialchars($contenido)); ?></p>
            </div>

            <!-- Imagen asociada -->
            <?php if (!empty($imagen)): ?>
                <div class="imagen-publicacion">
                    <img src="<?php echo htmlspecialchars($imagen); ?>" alt="Imagen de la publicación">
                </div>
            <?php endif; ?>
        </div>

        <div class="acciones">
            <a href="Principal.php" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

</body>

</html>

<?php
mysqli_close($enlace);
?>