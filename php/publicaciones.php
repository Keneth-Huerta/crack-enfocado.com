<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/publicaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- CDN de Font Awesome -->
    <title>Publicaciones</title>

    <style>
        /* Estilo para ocultar el formulario de comentario */
        .comment-form {
            display: none;
            margin-top: 10px;
        }

        /* Estilo para ocultar la lista de comentarios */
        .comments-list {
            display: none;
            margin-top: 10px;
        }
    </style>

</head>

<body>
    <?php include('header.php'); ?>
    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'conexion.php'; // Incluir la conexión a la base de datos
            if (session_status() == PHP_SESSION_NONE) {
            if (session_status() == PHP_SESSION_NONE) {
    session_start();
}        }

            if (!isset($_SESSION['usuario_id'])) {
                echo "<p>Debes <a href='../crearCuenta.html'>crear una cuenta</a> o <a href='../secion.php'>iniciar sesión</a> para poder publicar.</p>";
            } else {
            ?>
                <form action="subir_public.php" method="post" enctype="multipart/form-data">
                    <textarea name="contenido" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">Publicar</button>
                </form>
            <?php
            }
            ?>
        </div>

        <!-- Mostrar publicaciones -->
        <div class="publicaciones">
            <?php
            // Consulta para obtener las publicaciones con los datos del usuario
            $stmt = $enlace->prepare("SELECT publicaciones.*, perfiles.foto_perfil, perfiles.nombre 
                                  FROM publicaciones 
                                  JOIN perfiles ON publicaciones.usuario_id = perfiles.usuario_id
                                  ORDER BY publicaciones.fecha_publicada DESC");
            $stmt->execute();
            $publicaciones = $stmt->get_result();

            // Bucle while para mostrar publicaciones
            while ($publicacion = $publicaciones->fetch_assoc()) {
                echo '<div class="post-item">';

                // Contenido del encabezado (usuario y avatar)
                echo '<div class="post-header">';
                $foto_perfil = $publicacion['foto_perfil'] ? htmlspecialchars($publicacion['foto_perfil']) : '../media/user.png';
                $id_user = $publicacion['usuario_id'];
                echo '<div class="post-avatar">';
                echo '<a href="perfil.php?usuario_id=' . $id_user . '"><img src="' . $foto_perfil . '" alt="Foto de perfil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"></a>';
                echo '</div>';
                echo '<div class="post-username">' . htmlspecialchars($publicacion['nombre'] ?? 'Usuario Anónimo') . '</div>';
                echo '</div>';

                // Contenido de la publicación
                echo '<div class="post-content">' . htmlspecialchars($publicacion['contenido'] ?? 'Sin contenido') . '</div>';

                // Imagen de la publicación (si existe)
                if (!empty($publicacion['imagen'])) {
                    echo '<img src="' . htmlspecialchars($publicacion['imagen']) . '" alt="Imagen de publicación">';
                }

                // Verificar si el usuario ya dio "Me gusta"
                $stmt_likes = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
                $stmt_likes->bind_param("ii", $_SESSION['usuario_id'], $publicacion['id_publicacion']);
                $stmt_likes->execute();
                $like_check = $stmt_likes->get_result();
                $liked_class = (mysqli_num_rows($like_check) > 0) ? 'liked' : '';

                // Botón para dar like (AJAX)
                echo '<button type="button" class="btn-like ' . $liked_class . '" data-id="' . $publicacion['id_publicacion'] . '" onclick="toggleLike(' . $publicacion['id_publicacion'] . ')">';
                echo '<i class="fas fa-heart"></i> Me gusta (<span id="like-count-' . $publicacion['id_publicacion'] . '">' . $publicacion['cantidad_megusta'] . '</span>)';
                echo '</button>';

                // Botón de "Comentar"
                echo '<button type="button" onclick="toggleCommentSection(' . $publicacion['id_publicacion'] . ')">Comentar</button>';

                echo '</div>'; // Cierre del post-item

                // Mostrar comentarios
                echo '<div id="comments-section-' . $publicacion['id_publicacion'] . '" class="comments-list">';

                // Obtener los comentarios de esta publicación
                $stmt_comentarios = $enlace->prepare("SELECT comentarios.*, perfiles.nombre AS usuario_nombre 
                                                  FROM comentarios 
                                                  JOIN perfiles ON comentarios.usuario_id = perfiles.usuario_id 
                                                  WHERE comentarios.publicacion_id = ? 
                                                  ORDER BY comentarios.fecha_comentario ASC");
                $stmt_comentarios->bind_param("i", $publicacion['id_publicacion']);
                $stmt_comentarios->execute();
                $comentarios = $stmt_comentarios->get_result();

                // Mostrar cada comentario
                while ($comentario = $comentarios->fetch_assoc()) {
                    echo '<div class="comment-item">';
                    echo '<strong>' . htmlspecialchars($comentario['usuario_nombre']) . ':</strong>';
                    echo '<p>' . htmlspecialchars($comentario['contenido']) . '</p>';
                    echo '</div>';
                }

                // Formulario para agregar un comentario (inicialmente oculto)
                echo '<div id="comment-form-' . $publicacion['id_publicacion'] . '" class="comment-form">';
                echo '<form action="agregar_comentario.php" method="POST">';
                echo '<input type="hidden" name="publicacion_id" value="' . $publicacion['id_publicacion'] . '">';
                echo '<textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>';
                echo '<button type="submit">Comentar</button>';
                echo '</form>';
                echo '</div>'; // Cierre del div de formulario de comentarios

                echo '</div>'; // Cierre de comentarios
            }
            ?>
        </div> <!-- Cierre de publicaciones -->
    </div> <!-- Cierre de container -->


    <script>
        // Función para mostrar/ocultar sección de comentarios
        function toggleCommentSection(publicationId) {
            const commentsSection = document.getElementById(`comments-section-${publicationId}`);
            const commentForm = document.getElementById(`comment-form-${publicationId}`);

            if (commentsSection && commentForm) {
                // Toggle visibility
                if (commentsSection.style.display === 'block') {
                    commentsSection.style.display = 'none';
                    commentForm.style.display = 'none';
                } else {
                    commentsSection.style.display = 'block';
                    commentForm.style.display = 'block';
                }
            }
        }

        // Función para ajustar altura de textareas automáticamente
        function autoResizeTextarea() {
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        }

        // Función para manejar likes de forma asíncrona
        <?php
// dar_like.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexion.php';

function crearNotificacionLike($enlace, $id_publicacion, $usuario_id) {
    $stmt = $enlace->prepare("INSERT INTO notificaciones (usuario_id, publicacion_id, tipo) VALUES (?, ?, 'like')");
    $stmt->bind_param("ii", $usuario_id, $id_publicacion);
    $stmt->execute();
}

// Verificar conexión a la base de datos
if ($enlace->connect_error) {
    die("Error de conexión: " . $enlace->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar input
    $id_publicacion = filter_input(INPUT_POST, 'id_publicacion', FILTER_VALIDATE_INT);

    if (!$id_publicacion) {
        echo "error|ID de publicación inválido";
        exit();
    }

    // Verificar si existe la publicación primero
    $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "error|Publicación no encontrada";
        exit();
    }

    // Si no hay sesión iniciada, devolver el conteo actual de likes
    if (!isset($_SESSION['usuario_id'])) {
        $row = $result->fetch_assoc();
        echo "info|" . $row['cantidad_megusta'];
        exit();
    }

    try {
        $enlace->begin_transaction();

        // Verificar si ya existe el like
        $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insertar like
            $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Actualizar contador
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();

            // Crear notificación
            crearNotificacionLike($enlace, $id_publicacion, $_SESSION['usuario_id']);
        } else {
            // Eliminar like
            $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
            $stmt->execute();

            // Actualizar contador
            $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta - 1 WHERE id_publicacion = ?");
            $stmt->bind_param("i", $id_publicacion);
            $stmt->execute();
        }

        // Obtener nuevo conteo
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $enlace->commit();

        echo ($result->num_rows == 0 ? "liked" : "unliked") . "|" . $row['cantidad_megusta'];
    } catch (Exception $e) {
        $enlace->rollback();
        echo "error|" . $e->getMessage();
    }
}

        // Inicializar funciones cuando el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            autoResizeTextarea();
        });
    </script>

    <!-- Bootstrap JS (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>