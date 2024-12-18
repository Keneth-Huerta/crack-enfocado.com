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
            background-color: #4267B2;
            color: white;
            padding: 10px 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
        }

        .post-form {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .post-form textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            resize: none;
            font-size: 1rem;
        }

        .post-form button {
            background-color: #7d1b1b;;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
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
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .post-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 10px;
        }

        .post-username {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .post-content {
            font-size: 1rem;
            color: #333;
        }

        .post-actions {
            margin-top: 10px;
        }

        .post-actions button {
            background: none;
            border: none;
            color: #4267B2;
            cursor: pointer;
            margin-right: 10px;
            font-size: 0.9rem;
        }

        .post-actions button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">Mi Red Social Creativa</div>

    <div class="container">
        <!-- Formulario para crear una publicación -->
        <div class="post-form">
            <form action="" method="post">
                <textarea name="content" placeholder="¿Qué estás pensando?"></textarea>
                <button type="submit">Publicar</button>
            </form>
        </div>

        <!-- Mostrar publicaciones -->
        <?php
        $publicaciones = [
            ["username" => "Juan Pérez", "content" => "¡Hoy es un gran día! 😊"],
            ["username" => "Ana López", "content" => "Me encanta este lugar. 🌄"],
            ["username" => "Carlos Gómez", "content" => "¿Alguien tiene recomendaciones de películas? 🎥"],
        ];

        foreach ($publicaciones as $publicacion) {
            echo "<div class='post'>";
            echo "<div class='post-header'>";
            echo "<div class='post-avatar'></div>";
            echo "<div class='post-username'>{$publicacion['username']}</div>";
            echo "</div>";
            echo "<div class='post-content'>{$publicacion['content']}</div>";
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
