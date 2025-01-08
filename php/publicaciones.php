<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/publicaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Publicaciones</title>

    <style>
        .comment-form {
            display: none;
            margin-top: 10px;
        }

        .comments-list {
            display: none;
            margin-top: 10px;
        }

        .liked i {
            color: red;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'conexion.php';
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['usuario_id'])) {
                echo "<p>Debes <a href='../crearCuenta.html'>crear una cuenta</a> o <a href='../secion.php'>iniciar sesión</a> para poder publicar.</p>";
            } else {
            ?>
                <form action="subir_public.php" method="post" enctype="multipart/form-data">
                    <textarea name="contenido" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">Publicar</button>
                </form>
            <?php } ?>
        </div>

        <!-- Mostrar publicaciones -->
        <div class="publicaciones">
            <?php
            // Consulta para obtener las publicaciones con los datos del usuario
            $stmt = $enlace->prepare("
                SELECT publicaciones.*, perfiles.foto_perfil, perfiles.nombre 
                FROM publicaciones 
                JOIN perfiles ON publicaciones.usuario_id = perfiles.usuario_id
                ORDER BY publicaciones.fecha_publicada DESC
            ");
            $stmt->execute();
            $publicaciones = $stmt->get_result();

            // Mostrar cada publicacion
            while ($publicacion = $publicaciones->fetch_assoc()) {
                echo '<div class="post-item">';

                // Encabezado
                echo '<div class="post-header">';
                $foto_perfil = !empty($publicacion['foto_perfil']) ? htmlspecialchars($publicacion['foto_perfil']) : '../media/user.png';
                $id_user     = $publicacion['usuario_id'];
                echo '<div class="post-avatar">';
                echo '<a href="perfil.php?usuario_id=' . $id_user . '"><img src="' . $foto_perfil . '" alt="Foto de perfil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"></a>';
                echo '</div>';
                echo '<div class="post-username">' . htmlspecialchars($publicacion['nombre'] ?? 'Usuario Anónimo') . '</div>';
                echo '</div>';

                // Contenido de la publicación
                echo '<div class="post-content">' . htmlspecialchars($publicacion['contenido'] ?? 'Sin contenido') . '</div>';

                // Imagen de la publicación
                if (!empty($publicacion['imagen'])) {
                    echo '<img src="' . htmlspecialchars($publicacion['imagen']) . '" alt="Imagen de publicación">';
                }

                // Verificar si el usuario ya dio "Me gusta"
                $stmt_likes = $enlace->prepare("SELECT 1 FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
                $stmt_likes->bind_param("ii", $_SESSION['usuario_id'], $publicacion['id_publicacion']);
                $stmt_likes->execute();
                $like_check = $stmt_likes->get_result();
                $already_liked = ($like_check->num_rows > 0);

                // Clases para el botón de "Me gusta"
                $liked_class = $already_liked ? 'liked' : '';
                $heart_class = $already_liked ? 'fas fa-heart' : 'far fa-heart';

                // Botón "Me gusta"
                echo '<button type="button" class="btn-like ' . $liked_class . '" data-id="' . $publicacion['id_publicacion'] . '" onclick="toggleLike(' . $publicacion['id_publicacion'] . ')">';
                echo '<i class="' . $heart_class . '"></i> Me gusta (<span id="like-count-' . $publicacion['id_publicacion'] . '">' . $publicacion['cantidad_megusta'] . '</span>)';
                echo '</button>';

                // Botón de "Comentar"
                echo '<button type="button" onclick="toggleCommentSection(' . $publicacion['id_publicacion'] . ')">Comentar</button>';

                echo '</div>'; // post-item

                // Comentarios
                echo '<div id="comments-section-' . $publicacion['id_publicacion'] . '" class="comments-list">';
                $stmt_comentarios = $enlace->prepare("
                    SELECT comentarios.*, perfiles.nombre AS usuario_nombre 
                    FROM comentarios 
                    JOIN perfiles ON comentarios.usuario_id = perfiles.usuario_id 
                    WHERE comentarios.publicacion_id = ? 
                    ORDER BY comentarios.fecha_comentario ASC
                ");
                $stmt_comentarios->bind_param("i", $publicacion['id_publicacion']);
                $stmt_comentarios->execute();
                $comentarios = $stmt_comentarios->get_result();

                // Mostrar cada comentario
                while ($comentario = $comentarios->fetch_assoc()) {
                    echo '<div class="comment-item">';
                    echo '<strong>' . htmlspecialchars($comentario['usuario_nombre']) . ':</strong> ';
                    echo '<p>' . htmlspecialchars($comentario['contenido']) . '</p>';
                    echo '</div>';
                }

                // Formulario para comentar
                echo '<div id="comment-form-' . $publicacion['id_publicacion'] . '" class="comment-form">';
                echo '<form action="agregar_comentario.php" method="POST">';
                echo '<input type="hidden" name="publicacion_id" value="' . $publicacion['id_publicacion'] . '">';
                echo '<textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>';
                echo '<button type="submit">Comentar</button>';
                echo '</form>';
                echo '</div>'; // comment-form

                echo '</div>'; // comments-section
            }
            ?>
        </div> <!-- /publicaciones -->
    </div> <!-- /container -->


    <script>
        // Mostrar/ocultar comentarios
        function toggleCommentSection(publicationId) {
            const commentsSection = document.getElementById(`comments-section-${publicationId}`);
            const commentForm = document.getElementById(`comment-form-${publicationId}`);

            if (commentsSection && commentForm) {
                const isVisible = (commentsSection.style.display === 'block');
                commentsSection.style.display = isVisible ? 'none' : 'block';
                commentForm.style.display = isVisible ? 'none' : 'block';
            }
        }

        // Ajuste automático de alto en textareas
        function autoResizeTextarea() {
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        }

        // Manejar el like mediante fetch (AJAX)
        async function toggleLike(publicacionId) {
            try {
                const formData = new FormData();
                formData.append('id_publicacion', publicacionId);

                const response = await fetch('dar_like.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.text();
                // data llega con formato "liked|15" o "unliked|14" o "info|10" o "error|mensajeError"
                const [status, newCount] = data.split('|');

                const likeButton = document.querySelector(`.btn-like[data-id="${publicacionId}"]`);
                const likeCount = document.getElementById(`like-count-${publicacionId}`);
                const icon = likeButton.querySelector('i');

                switch (status) {
                    case 'liked':
                        likeButton.classList.add('liked');
                        icon.className = 'fas fa-heart';
                        if (newCount) likeCount.textContent = newCount;
                        break;

                    case 'unliked':
                        likeButton.classList.remove('liked');
                        icon.className = 'far fa-heart';
                        if (newCount) likeCount.textContent = newCount;
                        break;

                    case 'info':
                        // Usuario no logueado: solo actualizamos el contador y avisamos
                        if (newCount) likeCount.textContent = newCount;
                        alert('Debes iniciar sesión para dar "Me gusta"');
                        break;

                    case 'error':
                        console.error('Error en servidor:', newCount);
                        break;
                }
            } catch (error) {
                console.error('Error de fetch:', error);
            }
        }

        // Inicializar funciones
        document.addEventListener('DOMContentLoaded', function() {
            autoResizeTextarea();
        });
    </script>

    <!-- Bootstrap (opcional si usas clases de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>