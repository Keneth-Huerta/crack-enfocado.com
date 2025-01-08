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

        .container {
            max-width: 700px;
            /* Limit width for better readability */
            margin: 70px auto;
            /* Center container and add top margin */
            padding: 0 15px;
        }

        .post-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .post-form form {
            display: flex;
            flex-direction: column;
        }

        .post-form textarea {
            resize: none;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .post-form input[type="file"] {
            margin-bottom: 10px;
        }

        .post-form button {
            align-self: flex-end;
            background-color: #952F57;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .post-form button:hover {
            background-color: #6f1f42;
        }

        .post-item {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .post-avatar {
            margin-right: 10px;
        }

        .post-username {
            font-weight: bold;
            color: #333;
        }

        .post-content {
            margin-bottom: 15px;
        }

        .post-item img {
            max-width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .btn-like,
        .btn-like:hover {
            background-color: transparent;
            border: 1px solid #952F57;
            color: #952F57;
            margin-right: 10px;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-like.liked {
            background-color: #952F57;
            color: white;
        }

        .btn-like i {
            margin-right: 5px;
        }

        .comment-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .comments-list.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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
        // Improved toggleCommentSection function
        function toggleCommentSection(publicationId) {
            if (!publicationId) return; // Ensure publicationId is valid
            const commentsSection = document.getElementById(`comments-section-${publicationId}`);
            const commentForm = document.getElementById(`comment-form-${publicationId}`);

            if (commentsSection) {
                const isCurrentlyVisible = commentsSection.classList.contains('show');

                // Hide all comment sections first
                document.querySelectorAll('.comments-list').forEach(section => {
                    section.classList.remove('show');
                    const form = document.getElementById(`comment-form-${section.id.split('-').pop()}`);
                    if (form) form.style.display = 'none';
                });

                // Toggle current section if it wasn't already shown
                if (!isCurrentlyVisible) {
                    commentsSection.classList.add('show');
                    if (commentForm) {
                        commentForm.style.display = 'block';
                    }
                }
            }
        } // Improved validation for comment and post forms
        function validateForm(form) {
            const textarea = form.querySelector('textarea');
            if (!textarea.value.trim()) {
                alert('Por favor, escribe algo antes de publicar.');
                return false;
            }
            return true;
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
        async function toggleLike(publicacion_id) {
            const likeButton = document.querySelector(`.btn-like[data-id="${publicacion_id}"]`);
            const likeCount = document.getElementById(`like-count-${publicacion_id}`);

            try {
                const formData = new FormData();
                formData.append('id_publicacion', publicacion_id);

                const response = await fetch('dar_like.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.text();
                const [status, newCount] = data.split('|');

                if (status === 'error') {
                    alert('Debes iniciar sesión para dar like');
                    return;
                }

                // Actualizar UI
                if (status === 'liked' || status === 'unliked') {
                    likeButton.classList.toggle('liked', status === 'liked');
                    const icon = likeButton.querySelector('i');
                    icon.classList.toggle('fas', status === 'liked');
                    icon.classList.toggle('far', status === 'unliked');

                    if (newCount) {
                        likeCount.textContent = newCount;
                        // Añadir una pequeña animación al contador
                        likeCount.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            likeCount.style.transform = 'scale(1)';
                        }, 200);
                    }
                }
            } catch (error) {
                console.error('Error al procesar el like:', error);
            }
        }
        // Add event listeners to forms
        document.addEventListener('DOMContentLoaded', function() {
            autoResizeTextarea();

            // Add form validation
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(this)) {
                        e.preventDefault();
                    }
                });
            });

            // Optional: Add character limit to textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.setAttribute('maxlength', '500');
                textarea.addEventListener('input', function() {
                    const remaining = 500 - this.value.length;
                    const counter = this.nextElementSibling || document.createElement('div');
                    counter.textContent = `${remaining} caracteres restantes`;
                    counter.style.color = '#888';
                    counter.style.fontSize = '0.8em';
                    if (!counter.parentNode) this.parentNode.insertBefore(counter, this.nextSibling);
                });
            });
        });
    </script>

    <!-- Bootstrap JS (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>