<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $publicacionId = $_GET['id'];

    // Consulta mejorada para obtener datos de la publicación y el usuario
    $stmt = $enlace->prepare("SELECT p.*, pr.foto_perfil, pr.nombre, pr.apellido, 
                             (SELECT COUNT(*) FROM likes WHERE publicacion_id = p.id_publicacion) as likes_count,
                             (SELECT COUNT(*) FROM comentarios WHERE publicacion_id = p.id_publicacion) as comments_count
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
        /* ... [Previous styles remain the same] ... */

        .comment-error {
            color: #dc3545;
            margin-top: 0.5rem;
            display: none;
        }

        .like-animation {
            animation: likeAnimation 0.3s ease;
        }

        .comment-count {
            color: #666;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php include('header.php'); ?>

    <div class="container">
        <div class="publication-container">
            <!-- ... [Previous header and content sections remain the same] ... -->

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
                    <span class="comment-count" id="comment-count">
                        <?php echo $publicacion['comments_count']; ?>
                    </span>
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

                    spinner.style.display = 'block';
                    submitButton.disabled = true;
                    errorDiv.style.display = 'none';

                    try {
                        const formData = new FormData(this);
                        const response = await fetch('agregar_comentario.php', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Agregar el nuevo comentario al principio de la lista
                            const commentsList = document.getElementById('comments-list');
                            const newComment = createCommentElement(result.comment);
                            commentsList.insertBefore(newComment, commentsList.firstChild);

                            // Actualizar el contador de comentarios
                            const commentCount = document.getElementById('comment-count');
                            commentCount.textContent = parseInt(commentCount.textContent) + 1;

                            // Limpiar el formulario
                            this.reset();
                        } else {
                            errorDiv.textContent = result.error || 'Error al agregar el comentario';
                            errorDiv.style.display = 'block';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        errorDiv.textContent = 'Error al procesar la solicitud';
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
            div.innerHTML = `
                <div class="comment-header">
                    <img src="${comment.foto_perfil || '../media/user.png'}" class="comment-avatar" alt="Avatar">
                    <strong>${comment.nombre} ${comment.apellido}</strong>
                </div>
                <p>${comment.contenido}</p>
                <small class="text-muted">${comment.fecha_comentario}</small>
            `;
            return div;
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