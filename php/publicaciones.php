<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/publicaciones.css">
    
    <title>Publicaciones Creativas</title>
<body>
    <div class="navbar">Publicaciones</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <?php
            include 'basePublicacion.php';
            session_start();

            if (!isset($_SESSION['username'])) {
                echo "<p>Debes <a href='../php/registro.php'>crear una cuenta</a> o <a href='../index.html'>iniciar sesión</a> para publicar.</p>";
            } else {
            ?>
                <form action="registro.php" method="post" enctype="multipart/form-data">
                    <textarea name="content" placeholder="¿Qué estás pensando?"></textarea>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">Publicar</button>
                </form>
            <?php
            }
            ?>
        </div>

        <!-- Mostrar publicaciones -->
        <?php
        $stmt = $pdo->query("SELECT * FROM publicaciones ORDER BY usuario DESC");
        $publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($publicaciones as $publicacion) {
            echo "<div class='post'>";
            echo "<div class='post-header'>";
            echo "<div class='post-avatar'></div>";
            echo "<div class='post-username'>{$publicacion['username']}</div>";
            echo "</div>";
            echo "<div class='post-content'>{$publicacion['content']}</div>";
            if (!empty($publicacion['image_path'])) {
                echo "<img src='{$publicacion['image_path']}' alt='Imagen de publicación' class='post-image'>";
            }
            echo "<div class='post-actions'>";
            echo "<button>Me gusta</button>";
            echo "<button>Comentar</button>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>




<!-- submit_post.php -->
<?php
include 'basePublicacion.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: registro.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['contenido'];
    $username = $_SESSION['usuario'];
    $imagePath = null;

    // Procesar la imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $imageName = time() . '_' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    // Insertar la publicación en la base de datos
    $stmt = $pdo->prepare("INSERT INTO publicaciones (usuario, contenido, imagen) VALUES (:username, :content, :image_path)");
    $stmt->execute([
        ':usuario' => $username,
        ':contenido' => $content,
        ':imagen' => $imagePath
    ]);

    header("Location: index.html");
    exit;
}
?>