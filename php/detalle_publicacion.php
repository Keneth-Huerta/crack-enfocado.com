
<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $publicacionId = $_GET['id'];

    // Consulta mejorada para obtener datos de la publicación y el usuario
    $stmt = $enlace->prepare("SELECT p.*, pr.foto_perfil, pr.nombre, pr.apellido, 
                             (SELECT COUNT(*) FROM likes WHERE publicacion_id = p.id_publicacion) as likes_count
                             FROM publicaciones p 
                             JOIN perfiles pr ON p.usuario_id = pr.usuario_id 
                             WHERE p.id_publicacion = ?");
    $stmt->bind_param("i", $publicacionId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $publicacion = $resultado->fetch_assoc();
    } else {
        echo "Publicación no encontrada.";
        exit();
    }
} else {
    echo "ID de publicación no especificado.";
    exit();
}

// Verificar si el usuario actual dio like
$userLiked = false;
if (isset($_SESSION['usuario_id'])) {
    $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt->bind_param("ii", $_SESSION['usuario_id'], $publicacionId);
    $stmt->execute();
    $userLiked = $stmt->get_result()->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- Meta tags básicos -->
    <meta name="description" content="Red Social Académica del CECyT 3 'Estanislao Ramírez Ruiz'. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="keywords" content="CECyT 3, IPN, red social académica, estudiantes, materiales escolares">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta property="og:title" content="CECyT 3 - Red Social Académica">
    <meta property="og:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta property="og:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">
    <meta property="og:image:alt" content="CECyT 3 Red Social Académica">
    <meta property="og:site_name" content="CECyT 3">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta name="twitter:title" content="CECyT 3 - Red Social Académica">
    <meta name="twitter:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="twitter:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">

    <!-- WhatsApp -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="CECyT 3">

    <!-- Android -->
    <meta name="theme-color" content="#741931">
    <link rel="manifest" href="/manifest.json">

    <!-- Favicon y íconos -->
    <link rel="mask-icon" href="/media/safari-pinned-tab.svg" color="#741931">
    <link rel="shortcut icon" href="/media/logoweb.svg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicación - <?php echo substr(htmlspecialchars($publicacion['contenido']), 0, 50) . '...'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .publication-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            overflow: hidden;
        }

        .publication-header {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .user-info h4 {
            margin: 0;
            color: #333;
            font-size: 1.1rem;
        }

        .user-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .publication-image {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            background-color: #f8f9fa;
        }

        .publication-content {
            padding: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #333;
        }

        .publication-actions {
            padding: 1rem;
            border-top: 1px solid #eee;
            display: flex;
            gap: 1rem;
        }

        .btn-like {
            background: none;
            border: none;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-like:hover {
            background-color: #f8f9fa;
        }

        .btn-like.liked {
            color: #e74c3c;
        }

        .btn-like.liked i {
            animation: likeAnimation 0.3s ease;
        }

        .comments-section {
            padding: 1rem;
            background-color: #f8f9fa;
        }

        .comment-form {
            margin-bottom: 1rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: vertical;
            min-height: 60px;
        }

        .comment-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .comment-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        @keyframes likeAnimation {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .publication-image {
                max-height: 300px;
            }
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            margin-left: 10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .comment-error {
            display: none;
            color: #dc3545;
            margin-top: 10px;
            padding: 8px;
            border-radius: 4px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body class="bg-light">
    <?php include('header.php'); ?>

    <div class="container">
        <div class="publication-container">
            <!-- Encabezado con información del usuario -->
            <div class="publication-header">
                <img src="<?php echo !empty($publicacion['foto_perfil']) ? htmlspecialchars($publicacion['foto_perfil']) : '../media/user.png'; ?>"
                    class="user-avatar" alt="Foto de perfil">
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($publicacion['nombre'] . ' ' . $publicacion['apellido']); ?></h4>
                    <p><?php echo date("d/m/Y H:i", strtotime($publicacion['fecha_publicada'])); ?></p>
                </div>
            </div>

            <!-- Imagen de la publicación -->
            <?php if (!empty($publicacion['imagen'])): ?>
                <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>"
                    class="publication-image" alt="Imagen de la publicación">
            <?php endif; ?>

            <!-- Contenido de la publicación -->
            <div class="publication-content">
                <?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?>
            </div>

            <!-- Acciones (Like y Comentarios) -->
            <div class="publication-actions">
                <button type="button" class="btn-like <?php echo $userLiked ? 'liked' : ''; ?>"
                    data-id="<?php echo $publicacion['id_publicacion']; ?>"
                    onclick="toggleLike(this, <?php echo $publicacion['id_publicacion']; ?>)">
                    <i class="fa<?php echo $userLiked ? 's' : 'r'; ?> fa-heart"></i>
                    <span id="like-count-<?php echo $publicacion['id_publicacion']; ?>">
                        <?php echo $publicacion['likes_count']; ?>
                    </span>
                </button>
                <button type="button" class="btn-like" onclick="toggleComments()">
                    <i class="far fa-comment"></i>
                    Comentarios
                </button>
            </div>

            <!-- Sección de comentarios -->
            <div id="comments-section" class="comments-section">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="comment-form">
                        <form id="commentForm">
                            <input type="hidden" name="publicacion_id" value="<?php echo $publicacionId; ?>">
                            <textarea name="contenido" placeholder="Escribe un comentario..." required class="form-control"></textarea>
                            <div class="d-flex align-items-center mt-2">
                                <button type="submit" class="btn btn-primary">Comentar</button>
                                <div class="loading-spinner" id="comment-spinner"></div>
                            </div>
                            <div class="comment-error" id="comment-error"></div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Debes <a href="../secion.php">iniciar sesión</a> para comentar.
                    </div>
                <?php endif; ?>

                <div id="comments-list">
                    <?php
                    $stmt = $enlace->prepare("SELECT c.*, pr.foto_perfil, pr.nombre, pr.apellido 
                                            FROM comentarios c 
                                            JOIN perfiles pr ON c.usuario_id = pr.usuario_id 
                                            WHERE c.publicacion_id = ? 
                                            ORDER BY c.fecha_comentario DESC");
                    $stmt->bind_param("i", $publicacionId);
                    $stmt->execute();
                    $comentarios = $stmt->get_result();

                    while ($comentario = $comentarios->fetch_assoc()):
                    ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <img src="<?php echo !empty($comentario['foto_perfil']) ? htmlspecialchars($comentario['foto_perfil']) : '../media/user.png'; ?>"
                                    class="comment-avatar" alt="Avatar">
                                <strong><?php echo htmlspecialchars($comentario['nombre'] . ' ' . $comentario['apellido']); ?></strong>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($comentario['contenido'])); ?></p>
                            <small class="text-muted">
                                <?php echo date("d/m/Y H:i", strtotime($comentario['fecha_comentario'])); ?>
                            </small>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function toggleLike(button, publicacionId) {
            if (!<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                alert('Debes iniciar sesión para dar like');
                return;
            }

            try {
                const response = await fetch('toggle_like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `publicacion_id=${publicacionId}`
                });

                const data = await response.json();

                if (data.success) {
                    const likeCount = button.querySelector(`#like-count-${publicacionId}`);
                    const icon = button.querySelector('i');

                    button.classList.toggle('liked', data.liked);
                    icon.className = data.liked ? 'fas fa-heart' : 'far fa-heart';
                    icon.classList.add('like-animation');
                    likeCount.textContent = data.likes_count;

                    // Eliminar la clase de animación después de que termine
                    setTimeout(() => {
                        icon.classList.remove('like-animation');
                    }, 300);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const spinner = document.getElementById('comment-spinner');
                    const errorDiv = document.getElementById('comment-error');
                    const submitButton = this.querySelector('button[type="submit"]');
                    const textarea = this.querySelector('textarea');

                    if (!textarea.value.trim()) {
                        errorDiv.textContent = 'El comentario no puede estar vacío';
                        errorDiv.style.display = 'block';
                        return;
                    }

                    spinner.style.display = 'block';
                    submitButton.disabled = true;
                    errorDiv.style.display = 'none';

                    try {
                        const formData = new FormData(this);
                        const response = await fetch('agregar_comentario.php', {
                            method: 'POST',
                            body: formData
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

            const contenido = comment.contenido.replace(/\n/g, '<br>');

            div.innerHTML = `
        <div class="comment-header">
            <img src="${comment.foto_perfil || '../media/user.png'}" 
                 class="comment-avatar" 
                 alt="Avatar">
            <strong>${escapeHtml(comment.nombre)} ${escapeHtml(comment.apellido)}</strong>
        </div>
        <p>${escapeHtml(contenido)}</p>
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
            const isHidden = commentsSection.style.display === 'none';
            commentsSection.style.display = isHidden ? 'block' : 'none';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>