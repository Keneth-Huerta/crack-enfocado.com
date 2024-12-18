<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/publicaciones.css">
    <title>Publicaciones Creativas</title>
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
                    <input type="file" name="imagen" accept="image/*">
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
                echo '<img src="' . htmlspecialchars($publicacion['imagen'] ?? './media/logoweb.jpg') . '" alt="Imagen de publicación">';
                echo '<h3>' . htmlspecialchars($publicacion['usuario'] ?? 'Usuario Anónimo') . '</h3>';
                echo '<p>' . htmlspecialchars($publicacion['contenido'] ?? 'Sin contenido') . '</p>';
                echo '<a href="#">Leer más</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
