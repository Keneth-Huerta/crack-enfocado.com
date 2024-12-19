<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/publicaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- CDN de Font Awesome -->
    <title>Publicaciones</title>
</head>

<body>

    <div class="navbar">Publicaciones</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'conexion.php'; // Incluir la conexión a la base de datos
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['usuario'])) {
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
            $stmt = $enlace->query("SELECT publicaciones.*, perfiles.foto_perfil, perfiles.nombre FROM publicaciones 
                                 JOIN perfiles ON publicaciones.usuario_id = perfiles.usuario_id
                                 ORDER BY publicaciones.fecha_publicada DESC");

            // Bucle while para mostrar publicaciones
            while ($publicacion = $stmt->fetch_assoc()) {
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

                // Acciones (botones)
                echo '<form action="dar_like.php" method="post">';
                echo '<input type="hidden" name="id_publicacion" value="' . $publicacion['id_publicacion'] . '">';
                echo '<button type="submit" class="btn-like ' . $liked_class . '">';
                echo '<i class="fas fa-heart"></i> Me gusta (' . $publicacion['cantidad_megusta'] . ')';
                echo '</button>';
                echo '</form>';

                echo '<button>Comentar</button>';
                echo '<button>Compartir</button>';
                echo '</div>';
            }
            ?>
        </div>

    </div>
</body>

</html>