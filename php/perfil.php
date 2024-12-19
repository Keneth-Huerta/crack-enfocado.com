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

// Obtener publicaciones del usuario con manejo de errores
try {
    $publicaciones_query = "SELECT p.*, perf.foto_perfil, perf.nombre 
                           FROM publicaciones p
                           JOIN perfiles perf ON p.usuario_id = perf.usuario_id 
                           WHERE p.usuario_id = ? 
                           ORDER BY p.fecha_publicada DESC";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        .comment-form {
            display: none;
            margin-top: 10px;
        }
        .comments-list {
            display: none;
            margin-top: 10px;
        }
    </style>
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
        <div class="publicaciones">
            <h2>Mis Publicaciones</h2>
            <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
                    <div class="post-item">
                        <!-- Encabezado de la publicación -->
                        <div class="post-header">
                            <div class="post-avatar">
                                <a href="perfil.php?usuario_id=<?php echo $publicacion['usuario_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($publicacion['foto_perfil'] ?? '../media/user.png'); ?>" 
                                         alt="Foto de perfil" 
                                         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                </a>
                            </div>
                            <div class="post-username"><?php echo htmlspecialchars($publicacion['nombre'] ?? 'Usuario Anónimo'); ?></div>
                        </div>

                        <!-- Contenido de la publicación -->
                        <div class="post-content"><?php echo htmlspecialchars($publicacion['contenido'] ?? 'Sin contenido'); ?></div>

                        <!-- Imagen de la publicación -->
                        <?php if (!empty($publicacion['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de publicación">
                        <?php endif; ?>

                        <!-- Botones de interacción -->
                        <?php
                        $stmt_likes = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
                        $stmt_likes->bind_param("ii", $_SESSION['usuario_id'], $publicacion['id_publicacion']);
                        $stmt_likes->execute();
                        $like_check = $stmt_likes->get_result();
                        $liked_class = (mysqli_num_rows($like_check) > 0) ? 'liked' : '';
                        ?>
                        
                        <button type="button" class="btn-like <?php echo $liked_class; ?>" 
                                data-id="<?php echo $publicacion['id_publicacion']; ?>" 
                                onclick="toggleLike(<?php echo $publicacion['id_publicacion']; ?>)">
                            <i class="fas fa-heart"></i> Me gusta 
                            (<span id="like-count-<?php echo $publicacion['id_publicacion']; ?>">
                                <?php echo $publicacion['cantidad_megusta']; ?>
                            </span>)
                        </button>

                        <button type="button" onclick="toggleCommentSection(<?php echo $publicacion['id_publicacion']; ?>)">
                            Comentar
                        </button>

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
                            ?>

                            <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                                <div class="comment-item">
                                    <strong><?php echo htmlspecialchars($comentario['usuario_nombre']); ?>:</strong>
                                    <p><?php echo htmlspecialchars($comentario['contenido']); ?></p>
                                </div>
                            <?php endwhile; ?>

                            <!-- Formulario para agregar comentarios -->
                            <div id="comment-form-<?php echo $publicacion['id_publicacion']; ?>" class="comment-form">
                                <form action="agregar_comentario.php" method="POST">
                                    <input type="hidden" name="publicacion_id" value="<?php echo $publicacion['id_publicacion']; ?>">
                                    <textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>
                                    <button type="submit">Comentar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
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
        // Debounce function para prevenir múltiples clicks rápidos
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Control de estado para los likes
        const likeStates = new Map();

        // Función mejorada para manejar likes
        async function toggleLike(publicacion_id) {
            const likeButton = document.querySelector(`.btn-like[data-id="${publicacion_id}"]`);
            const likeCount = document.getElementById(`like-count-${publicacion_id}`);

            // Prevenir múltiples clicks mientras se procesa
            if (likeStates.get(publicacion_id)) return;
            likeStates.set(publicacion_id, true);

            try {
                // Añadir clase de animación
                likeButton.classList.add('clicked');

                const formData = new FormData();
                formData.append('id_publicacion', publicacion_id);

                const response = await fetch('dar_like.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.text();

                // Actualizar el contador y el estado visual
                if (data.includes('Like agregado!')) {
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    likeButton.classList.add('liked');
                } else if (data.includes('Like eliminado!')) {
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    likeButton.classList.remove('liked');
                }
            } catch (error) {
                console.error('Error al procesar el like:', error);
            } finally {
                // Limpiar el estado después de un breve delay
                setTimeout(() => {
                    likeButton.classList.remove('clicked');
                    likeStates.set(publicacion_id, false);
                }, 300);
            }
        }

        // Inicializar los botones de like cuando el DOM esté listo
        function initializeLikeButtons() {
            const buttons = document.querySelectorAll('.btn-like');

            buttons.forEach(button => {
                const debouncedToggleLike = debounce((id) => toggleLike(id), 300);

                button.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    const id = button.dataset.id;
                    if (!likeStates.get(id)) {
                        button.classList.add('clicked');
                    }
                });

                button.addEventListener('touchend', (e) => {
                    e.preventDefault();
                    const id = button.dataset.id;
                    if (!likeStates.get(id)) {
                        debouncedToggleLike(id);
                    }
                });

                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const id = button.dataset.id;
                    if (!likeStates.get(id)) {
                        debouncedToggleLike(id);
                    }
                });

                likeStates.set(button.dataset.id, false);
            });
        }

        document.addEventListener('DOMContentLoaded', initializeLikeButtons);

        // Función para actualizar botones después de cambios dinámicos
        function updateLikeButtons() {
            initializeLikeButtons();
        }

        // Función para mostrar/ocultar comentarios
        function toggleCommentSection(publicacion_id) {
            var commentsSection = document.getElementById('comments-section-' + publicacion_id);
            var commentForm = document.getElementById('comment-form-' + publicacion_id);

            if (commentsSection.style.display === "none" || commentsSection.style.display === "") {
                commentsSection.style.display = "block";
                commentForm.style.display = "block";
            } else {
                commentsSection.style.display = "none";
                commentForm.style.display = "none";
            }
        }
    </script>
</body>
</html>

<?php
if (isset($stmt_publicaciones)) {
    mysqli_stmt_close($stmt_publicaciones);
}
mysqli_close($enlace);
?>