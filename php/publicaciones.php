<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/publicaciones.css">
    <title>Publicaciones</title>
</head>
<body>
    <div class="navbar">Publicaciones</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'basePublicacion.php';
            session_start();

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
            // Consulta para obtener las publicaciones
            $stmt = $pdo->query("SELECT * FROM publicaciones ORDER BY usuario DESC");

            // Bucle while para mostrar publicaciones
                while ($publicacion = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="post-item">';
        
            // Contenido del encabezado (usuario y avatar)
            echo '<div class="post-header">';
            echo '<div class="post-avatar"></div>';
            echo '<div class="post-username">' . htmlspecialchars($publicacion['usuario'] ?? 'Usuario Anónimo') . '</div>';
            echo '</div>';
        
            // Contenido de la publicación
            echo '<div class="post-content">' . htmlspecialchars($publicacion['contenido'] ?? 'Sin contenido') . '</div>';
        
            // Imagen de la publicación (si existe)
            if (!empty($publicacion['imagen'])) {
                echo '<img src="' . htmlspecialchars($publicacion['imagen']) . '" alt="Imagen de publicación">';
            }
        
            // Acciones (botones)
            <button type="submit">Me gusta</button>
            <button type="submit">comentar</button>
            <button type="submit">Compartir</button>
           /* echo '<div class="post-actions">';
            echo '<button>Me gusta</button>';

            echo '<button>Comentar</button>';
            echo '<button>Compartir</button>';
            echo '</div>';*/
        
            echo '</div>';
        }
        ?>
        </div>

    </div>
</body>
</html>
