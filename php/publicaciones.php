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
    <div class="navb">Publicaciones</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'conexion.php'; // Incluir la conexión a la base de datos
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['usuario_id'])) {
                echo "<p>Debes <a href='../crearCuenta.html'>crear una cuenta</a> o <a href='../index.html'>iniciar sesión</a> para poder publicar.</p>";
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
                $foto_perfil = $publicacion['foto_perfil'] ? htmlspecialchars($publicacion['foto_perfil']) : 'default-profile.jpg';
                echo '<div class="post-avatar">';
                echo '<img src="' . $foto_perfil . '" alt="Foto de perfil" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">';
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

                echo '</div>';

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
                echo '</div>'; // Cierre de post-item
            }
            ?>
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

        // Manejador de eventos para los botones de like
        function initializeLikeButtons() {
            const buttons = document.querySelectorAll('.btn-like');

            buttons.forEach(button => {
                // Crear versión con debounce del toggleLike
                const debouncedToggleLike = debounce((id) => toggleLike(id), 300);

                // Manejar eventos touch
                button.addEventListener('touchstart', (e) => {
                    e.preventDefault(); // Prevenir comportamiento por defecto
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

                // Manejar clicks normales
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const id = button.dataset.id;
                    if (!likeStates.get(id)) {
                        debouncedToggleLike(id);
                    }
                });

                // Inicializar el estado
                likeStates.set(button.dataset.id, false);
            });
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', initializeLikeButtons);

        // Función para actualizar botones después de cambios dinámicos en el DOM
        function updateLikeButtons() {
            initializeLikeButtons();
        }

        // Mantener la función toggleCommentSection que ya tenías
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