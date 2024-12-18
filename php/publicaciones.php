<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones Creativas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: #7d1b1b;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
        }

        .post-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .post-form textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            resize: none;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .post-form input[type="file"] {
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .post-form button {
            background-color: #7d1b1b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .post-form button:hover {
            background-color: #7d1b1b;
        }

        .post {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .post-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 15px;
        }

        .post-username {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
        }

        .post-content {
            font-size: 1rem;
            color: #444;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .post-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .post-actions {
            margin-top: 10px;
            display: flex;
            gap: 15px;
        }

        .post-actions button {
            background: none;
            border: none;
            color: #7d1b1b;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: bold;
        }

        .post-actions button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">Publicaciones</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <form action="" method="post" enctype="multipart/form-data">
                <textarea name="content" placeholder="¿Qué estás pensando?"></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Publicar</button>
            </form>
        </div>

        <!-- Mostrar publicaciones -->
        <?php
        $publicaciones = [
            ["username" => "Juan Pérez", "content" => "¡Hoy es un gran día! 😊", "image" => "https://via.placeholder.com/600"],
            ["username" => "Ana López", "content" => "Me encanta este lugar. 🌄", "image" => "https://via.placeholder.com/600"],
            ["username" => "Carlos Gómez", "content" => "¿Alguien tiene recomendaciones de películas? 🎥", "image" => ""]
        ];

        foreach ($publicaciones as $publicacion) {
            echo "<div class='post'>";
            echo "<div class='post-header'>";
            echo "<div class='post-avatar'></div>";
            echo "<div class='post-username'>{$publicacion['username']}</div>";
            echo "</div>";
            echo "<div class='post-content'>{$publicacion['content']}</div>";
            if (!empty($publicacion['image'])) {
                echo "<img src='{$publicacion['image']}' alt='Imagen de publicación' class='post-image'>";
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
