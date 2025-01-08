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

        .like-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .like-count {
            font-size: 0.9em;
            color: #666;
        }

        .comment-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
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
            // Consulta para obtener las publicaciones con los datos del usuario y cantidad de likes
            $stmt = $enlace->prepare("
                SELECT 
                    p.*, 
                    perf.foto_perfil, 
                    perf.nombre,
                    COUNT(DISTINCT l.like_id) as likes_count,
                    EXISTS(
                        SELECT 1 
                        FROM likes 
                        WHERE publicacion_id = p.id_publicacion 
                        AND usuario_id = ?
                    ) as user_liked
                FROM publicaciones p
                JOIN perfiles perf ON p.usuario_id = perf.usuario_id
                LEFT JOIN likes l ON p.id_publicacion = l.publicacion_id
                GROUP BY p.id_publicacion
                ORDER BY p.fecha_publicada DESC
            ");
            $usuario_actual = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;
            $stmt->bind_param("i", $usuario_actual);
            $stmt->execute();
            $publicaciones = $stmt->get_result();

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

                // Botón de "Me gusta"
                $liked_class = $publicacion['user_liked'] ? 'liked' : '';
                echo '<button class="like-button ' . $liked_class . '" onclick="toggleLike(' . $publicacion['id_publicacion'] . ')" data-publication-id="' . $publicacion['id_publicacion'] . '">';
                echo '<i class="fas fa-heart"></i>';
                echo '<span class="like-count">' . $publicacion['likes_count'] . '</span>';
                echo '</button>';

                // Botón de "Comentar"
                echo '<button type="button" onclick="toggleCommentSection(' . $publicacion['id_publicacion'] . ')">Comentar</button>';

                // Sección de comentarios
                echo '<div id="comments-section-' . $publicacion['id_publicacion'] . '" class="comments-list">';

                // Formulario de comentarios siempre antes de la lista de comentarios
                if (isset($_SESSION['usuario_id'])) {
                    echo '<div class="comment-form" style="display: block;">'; // Quitamos el ID y dejamos visible
                    echo '<form onsubmit="submitComment(event, ' . $publicacion['id_publicacion'] . ')">';
                    echo '<textarea name="contenido_comentario" placeholder="Escribe un comentario..." required></textarea>';
                    echo '<button type="submit">Comentar</button>';
                    echo '</form>';
                    echo '</div>';
                }

                // Lista de comentarios
                echo '<div id="comments-list-' . $publicacion['id_publicacion'] . '" class="comments-container">';
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

                while ($comentario = $comentarios->fetch_assoc()) {
                    echo '<div class="comment-item">';
                    echo '<strong>' . htmlspecialchars($comentario['usuario_nombre']) . ':</strong> ';
                    echo '<p>' . htmlspecialchars($comentario['contenido']) . '</p>';
                    echo '</div>';
                }
                echo '</div>'; // comments-container
                echo '</div>'; // comments-section
                echo '</div>'; // post-item
            }
            ?>
        </div>
    </div>

    <script>
        async function toggleLike(publicationId) {
            if (!<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                alert('Debes iniciar sesión para dar me gusta');
                return;
            }

            try {
                const response = await fetch('toggle_like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `publicacion_id=${publicationId}`
                });

                if (!response.ok) throw new Error('Error en la respuesta del servidor');

                const data = await response.json();
                const button = document.querySelector(`button[data-publication-id="${publicationId}"]`);
                const likeCount = button.querySelector('.like-count');

                if (data.liked) {
                    button.classList.add('liked');
                } else {
                    button.classList.remove('liked');
                }

                likeCount.textContent = data.likes_count;
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar tu me gusta');
            }
        }

        function toggleCommentSection(publicationId) {
            const commentsSection = document.getElementById(`comments-section-${publicationId}`);
            const isVisible = commentsSection.style.display === 'block';
            commentsSection.style.display = isVisible ? 'none' : 'block';
        }

        async function submitComment(event, publicationId) {
            event.preventDefault();

            const form = event.target;
            const textarea = form.querySelector('textarea');
            const contenido = textarea.value.trim();

            if (!contenido) {
                alert('El comentario no puede estar vacío');
                return;
            }

            try {
                const response = await fetch('agregar_comentario.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        publicacion_id: publicationId,
                        contenido: contenido
                    })
                });

                if (!response.ok) throw new Error('Error en la respuesta del servidor');

                const data = await response.json();

                if (data.success) {
                    // Agregar el nuevo comentario al DOM
                    const commentsList = document.getElementById(`comments-list-${publicationId}`);
                    const newComment = document.createElement('div');
                    newComment.className = 'comment-item';
                    newComment.innerHTML = `
                        <strong>${data.comment.nombre}:</strong>
                        <p>${data.comment.contenido}</p>
                    `;
                    commentsList.appendChild(newComment);

                    // Limpiar el textarea
                    textarea.value = '';
                } else {
                    alert(data.error || 'Error al agregar el comentario');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar el comentario');
            }
        }

        function autoResizeTextarea() {
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const spinner = document.getElementById('comment-spinner');
                    const errorDiv = document.getElementById('comment-error');
                    const submitButton = this.querySelector('button[type="submit"]');
                    const textarea = this.querySelector('textarea[name="contenido"]');
                    const contenido = textarea.value.trim();
                    const publicacionId = this.querySelector('input[name="publicacion_id"]').value;

                    if (!contenido) {
                        errorDiv.textContent = 'El comentario no puede estar vacío';
                        errorDiv.style.display = 'block';
                        return;
                    }

                    spinner.style.display = 'block';
                    submitButton.disabled = true;
                    errorDiv.style.display = 'none';

                    try {
                        const response = await fetch('agregar_comentario.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                publicacion_id: publicacionId,
                                contenido: contenido
                            })
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const result = await response.json();

                        if (result.success) {
                            // Limpiar el formulario
                            this.reset();

                            // Crear y agregar el nuevo comentario
                            const commentsList = document.getElementById('comments-list');
                            const newComment = createCommentElement(result.comment);

                            if (commentsList.firstChild) {
                                commentsList.insertBefore(newComment, commentsList.firstChild);
                            } else {
                                commentsList.appendChild(newComment);
                            }
                        } else {
                            throw new Error(result.error || 'Error al agregar el comentario');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        errorDiv.textContent = error.message || 'Error al procesar la solicitud';
                        errorDiv.style.display = 'block';
                    } finally {
                        spinner.style.display = 'none';
                        submitButton.disabled = false;
                    }
                });
            }
        });

        function createCommentElement(comment) {
            const div = document.createElement('div');
            div.className = 'comment-item';

            div.innerHTML = `
        <div class="comment-header">
            <img src="${comment.foto_perfil || '../media/user.png'}" 
                 class="comment-avatar" 
                 alt="Avatar">
            <strong>${escapeHtml(comment.nombre)}</strong>
        </div>
        <p>${escapeHtml(comment.contenido)}</p>
        <small class="text-muted">${comment.fecha_comentario}</small>
    `;
            return div;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function toggleComments() {
            const commentsSection = document.getElementById('comments-section');
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>