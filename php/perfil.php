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
    // Si no se pasa el ID de usuario, redirigir al perfil del usuario actual
    $usuario_ids = $_SESSION['usuario_id'];
}
$perfil = [];
$publicaciones = [];


// Obtener publicaciones del usuario con manejo de errores
try {
    $publicaciones_query = "SELECT id_publicacion, contenido, imagen, fecha_publicada, usuario_id 
                            FROM publicaciones 
                            WHERE usuario_id = ? 
                            ORDER BY fecha_publicada DESC";

    if ($stmt_publicaciones = mysqli_prepare($enlace, $publicaciones_query)) {
        mysqli_stmt_bind_param($stmt_publicaciones, "i", $usuario_ids);

        if (!mysqli_stmt_execute($stmt_publicaciones)) {
            throw new Exception("Error al obtener publicaciones: " . mysqli_error($enlace));
        }

        $publicaciones_result = mysqli_stmt_get_result($stmt_publicaciones);
    } else {
        throw new Exception("Error en la preparación de la consulta de publicaciones");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $publicaciones_result = false;
}

// Obtener perfil con manejo de errores mejorado
try {
    $query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $usuario_ids);

        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            $perfil = mysqli_fetch_assoc($resultado);
        } else {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_error($enlace));
        }
        mysqli_stmt_close($stmt);
    } else {
        throw new Exception("Error en la preparación de la consulta: " . mysqli_error($enlace));
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
            <!-- Foto de perfil -->
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
        <?php if ($usuario_ids == $_SESSION['usuario_id']): ?>

            <link rel="stylesheet" href="../css/misestilos.css">
            <div class="acciones">
                <a href="editar_perfil.php" class="btn-editar">
                    <i class="fas fa-edit"></i> Editar perfil
                </a>
                <a href="logout.php" class="btn-cerrar-sesion">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        <?php endif; ?>

        <!-- Mostrar publicaciones del usuario -->
        <div class="publicaciones-usuario">
            <h2>Mis Publicaciones</h2>
            <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                <div class="lista-publicaciones">
    <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
        <div class="post-item">
            <!-- Encabezado de la publicación -->
            <div class="post-header">
                <img src="<?php echo htmlspecialchars($foto_perfils); ?>" alt="Foto de perfil" class="foto-perfil-publicacion">
                <div class="post-usuario"><?php echo htmlspecialchars($nombre); ?></div>
                <div class="post-fecha"><?php echo htmlspecialchars($publicacion['fecha_publicada']); ?></div>
            </div>

            <!-- Contenido de la publicación -->
            <div class="post-contenido"><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></div>

            <!-- Imagen de la publicación -->
            <?php if (!empty($publicacion['imagen'])): ?>
                <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de la publicación" class="post-imagen">
            <?php endif; ?>

            <!-- Sección de botones: Me gusta y comentar -->
            <div class="post-botones">
                <!-- Botón de "Me gusta" -->
                <?php
                $stmt_likes = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
                $stmt_likes->bind_param("ii", $_SESSION['usuario_id'], $publicacion['id_publicacion']);
                $stmt_likes->execute();
                $like_check = $stmt_likes->get_result();
                $liked_class = (mysqli_num_rows($like_check) > 0) ? 'liked' : '';
                ?>
                <button class="btn-like <?php echo $liked_class; ?>" data-id="<?php echo $publicacion['id_publicacion']; ?>" onclick="toggleLike(<?php echo $publicacion['id_publicacion']; ?>)">
                    <i class="fas fa-heart"></i> Me gusta (<span id="like-count-<?php echo $publicacion['id_publicacion']; ?>"><?php echo $publicacion['cantidad_megusta']; ?></span>)
                </button>

                <!-- Botón de "Comentar" -->
                <button type="button" onclick="toggleCommentSection(<?php echo $publicacion['id_publicacion']; ?>)">Comentar</button>
            </div>

            <!-- Sección de comentarios -->
            <div id="comments-section-<?php echo $publicacion['id_publicacion']; ?>" class="comments-list">
                <?php
                $stmt_comentarios = $enlace->prepare("SELECT comentarios.*, perfiles.nombre AS usuario_nombre 
                                                      FROM comentarios 
                                                      JOIN perfiles ON comentarios.usuario_id = perfiles.usuario_id 
                                                      WHERE comentarios.publicacion_id = ? 
                                                      ORDER BY comentarios.fecha_comentario ASC");
                $stmt_comentarios->bind_param("i", $publicacion['id_publicacion']);
                $stmt_comentarios->execute();
                $comentarios = $stmt_comentarios->get_result();

                while ($comentario = $comentarios->fetch_assoc()):
                ?>
                    <div class="comment-item">
                        <strong><?php echo htmlspecialchars($comentario['usuario_nombre']); ?>:</strong>
                        <p><?php echo htmlspecialchars($comentario['contenido']); ?></p>
                    </div>
                <?php endwhile; ?>

                <!-- Formulario para agregar un comentario -->
                <div id="comment-form-<?php echo $publicacion['id_publicacion']; ?>" class="comment-form">
                    <form action="agregar_comentario.php" method="POST">
                        <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                        <textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>
                        <button type="submit">Comentar</button>
                    </form>
                </div>
            </div>
        </div>
            <?php else: ?>
                <div class="sin-publicaciones">
                    <p><i class="far fa-newspaper"></i> Aún no has realizado ninguna publicación.</p>
                    <a href="publicaciones.php" class="btn-nueva-publicacion">
                        <i class="fas fa-plus"></i> Crear primera publicación
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Script para lazy loading de imágenes
        document.addEventListener('DOMContentLoaded', function() {
            var lazyImages = [].slice.call(document.querySelectorAll("img[loading='lazy']"));

            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.removeAttribute('loading');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }
        });
    </script>
</body>

</html>

<?php
if (isset($stmt_publicaciones)) {
    mysqli_stmt_close($stmt_publicaciones);
}
mysqli_close($enlace);
?>