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
                    onclick="toggleLike(<?php echo $publicacion['id_publicacion']; ?>)">
                    <i class="fa<?php echo $userLiked ? 's' : 'r'; ?> fa-heart"></i>
                    <span id="like-count-<?php echo $publicacion['id_publicacion']; ?>">
                        <?php echo $publicacion['likes_count']; ?>
                    </span>
                </button>
                <button type="button" class="btn-like" onclick="toggleComments()">
                    <i class="far fa-comment"></i> Comentarios
                </button>
            </div>

            <!-- Sección de comentarios -->
            <div id="comments-section" class="comments-section" style="display: none;">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="comment-form">
                        <form id="commentForm" onsubmit="return submitComment(event)">
                            <input type="hidden" name="publicacion_id" value="<?php echo $publicacionId; ?>">
                            <textarea name="contenido" placeholder="Escribe un comentario..." required></textarea>
                            <button type="submit" class="btn btn-primary mt-2">Comentar</button>
                        </form>
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
        async function toggleLike(publicacionId) {
            if (!<?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>) {
                alert('Debes iniciar sesión para dar like');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('id_publicacion', publicacionId);

                const response = await fetch('dar_like.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.text();
                const [status, newCount] = data.split('|');

                const likeButton = document.querySelector(`.btn-like[data-id="${publicacionId}"]`);
                const likeCount = document.getElementById(`like-count-${publicacionId}`);
                const icon = likeButton.querySelector('i');

                if (status === 'liked' || status === 'unliked') {
                    likeButton.classList.toggle('liked', status === 'liked');
                    icon.className = status === 'liked' ? 'fas fa-heart' : 'far fa-heart';
                    if (newCount) likeCount.textContent = newCount;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function toggleComments() {
            const commentsSection = document.getElementById('comments-section');
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }

        async function submitComment(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('agregar_comentario.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.text();
                if (result === 'success') {
                    location.reload(); // Recargar para mostrar el nuevo comentario
                } else {
                    alert('Error al agregar el comentario');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>