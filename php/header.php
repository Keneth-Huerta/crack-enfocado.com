<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Manejo de errores personalizado
function handleDatabaseError($error)
{
    error_log("Error de base de datos: " . $error);
    return null;
}
// Mejorada la función para obtener el path correcto de la foto de perfil
function getProfilePhotoPath($profilePhoto)
{
    if (empty($profilePhoto)) {
        return '/media/default-profile.jpg';
    }

    // Asegurarse de que la ruta comience con /
    return strpos($profilePhoto, '/') === 0 ? $profilePhoto : '/' . $profilePhoto;
}
// Función mejorada para obtener información del perfil
function getUserProfile($enlace, $usuario_id)
{
    try {
        $query = "SELECT p.*, u.username, u.email, u.foto_perfil 
                 FROM perfiles p 
                 JOIN usuarios u ON p.usuario_id = u.id 
                 WHERE p.usuario_id = ?";

        $stmt = mysqli_prepare($enlace, $query);
        if (!$stmt) {
            throw new Exception(mysqli_error($enlace));
        }

        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(mysqli_stmt_error($stmt));
        }

        $resultado = mysqli_stmt_get_result($stmt);
        $perfil = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);

        if ($perfil) {
            $perfil['foto_perfil'] = getProfilePhotoPath($perfil['foto_perfil']);
        }

        return $perfil;
    } catch (Exception $e) {
        handleDatabaseError($e->getMessage());
        return null;
    }
}

// Función mejorada para obtener notificaciones
function getUserNotifications($enlace, $usuario_id)
{
    try {
        $query = "SELECT n.*, 
                        CASE 
                            WHEN n.tipo = 'venta' THEN CONCAT('/ventas/detalle.php?id=', n.referencia_id)
                            WHEN n.tipo = 'mensaje' THEN CONCAT('/mensajes/ver.php?id=', n.referencia_id)
                            WHEN n.tipo = 'perfil' THEN CONCAT('/perfil/ver.php?id=', n.referencia_id)
                            ELSE '#'
                        END as enlace
                 FROM notificaciones n 
                 WHERE n.usuario_id = ? 
                 AND n.leida = 0 
                 ORDER BY n.fecha DESC 
                 LIMIT 5";

        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } catch (Exception $e) {
        handleDatabaseError($e->getMessage());
        return [];
    }
}
// Función para marcar notificaciones como leídas
function markNotificationAsRead($enlace, $notificacion_id)
{
    try {
        $query = "UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "ii", $notificacion_id, $_SESSION['usuario_id']);
        return mysqli_stmt_execute($stmt);
    } catch (Exception $e) {
        handleDatabaseError($e->getMessage());
        return false;
    }
}
// Función para crear una nueva notificación
function createNotification($enlace, $usuario_id, $tipo, $mensaje, $referencia_id = null)
{
    try {
        $query = "INSERT INTO notificaciones (usuario_id, tipo, mensaje, referencia_id, fecha) 
                 VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "issi", $usuario_id, $tipo, $mensaje, $referencia_id);
        return mysqli_stmt_execute($stmt);
    } catch (Exception $e) {
        handleDatabaseError($e->getMessage());
        return false;
    }
}


require_once 'conexion.php';

// Inicializar variables
$perfil = null;
$notificaciones = [];
$foto_perfil = '../media/default-profile.jpg';
$nombre_usuario = '';

// Obtener información del usuario si está logueado
if (isset($_SESSION['usuario_id'])) {
    $perfil = getUserProfile($enlace, $_SESSION['usuario_id']);
    if ($perfil) {
        $foto_perfil = $perfil['foto_perfil'];
        $nombre_usuario = htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']);
        $notificaciones = getUserNotifications($enlace, $_SESSION['usuario_id']);
    }
}
// Detectar la página actual para resaltar el ítem del menú
$current_page = basename($_SERVER['PHP_SELF']);

// Marcar notificación como leída si se hace clic en ella
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    markNotificationAsRead($enlace, $_POST['notification_id']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>

<!--- CSS --->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1050;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        background-color: #952F57;
        padding: 0.5rem 1rem;
    }

    body {
        padding-top: 70px;
    }

    .nav-link {
        position: relative;
        color: white !important;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: white;
    }

    .profile-dropdown {
        min-width: 250px;
        padding: 1rem;
    }

    .profile-header {
        display: flex;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        padding: 0.25rem 0.5rem;
        border-radius: 50%;
        background-color: #dc3545;
        color: white;
        font-size: 0.75rem;
    }

    .notification-item {
        padding: 0.5rem;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s ease;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .search-form {
        position: relative;
    }

    .search-form .form-control {
        border-radius: 20px;
        padding-left: 2.5rem;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .search-form .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .search-form .bi-search {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
    }

    .search-form .form-control:focus {
        background-color: white;
        color: #212529;
    }

    .search-form .form-control:focus::placeholder {
        color: #6c757d;
    }

    .search-form .form-control:focus+.bi-search {
        color: #6c757d;
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="../index.php">
            <img src="../media/logoweb.svg" alt="Logo" class="img-fluid" style="max-height: 50px;">
        </a>

        <!-- Botón hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido del navbar -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Búsqueda -->
            <form class="search-form d-flex me-auto" action="busqueda.php" method="GET">
                <i class="bi bi-search"></i>
                <input class="form-control" type="search" name="search" placeholder="Buscar..." aria-label="Buscar">
            </form>

            <!-- Menú de navegación -->
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'Principal.php' ? 'active' : ''; ?>"
                        href="Principal.php">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'publicaciones.php' ? 'active' : ''; ?>"
                        href="publicaciones.php">
                        <i class="bi bi-file-text"></i> Publicaciones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'ventas.php' ? 'active' : ''; ?>"
                        href="ventas.php">
                        <i class="bi bi-shop"></i> Ventas
                    </a>
                </li>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <!-- Notificaciones -->
                    <div class="dropdown-menu dropdown-menu-end">
                        <?php if (empty($notificaciones)): ?>
                            <div class="dropdown-item text-muted">No hay notificaciones nuevas</div>
                        <?php else: ?>
                            <?php foreach ($notificaciones as $notif): ?>
                                <form method="POST" class="notification-form">
                                    <input type="hidden" name="notification_id" value="<?php echo $notif['id']; ?>">
                                    <input type="hidden" name="mark_read" value="1">
                                    <button type="submit" class="dropdown-item notification-item">
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notif['fecha'])); ?></small>
                                        <div><?php echo htmlspecialchars($notif['mensaje']); ?></div>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="notificaciones.php">Ver todas</a>
                        <?php endif; ?>
                    </div>

                    <!-- Perfil -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                                alt="Perfil"
                                class="rounded-circle"
                                style="width: 40px; height: 40px; object-fit: cover;">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                            <div class="profile-header">
                                <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                                    alt="Perfil"
                                    class="rounded-circle me-2"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0"><?php echo $nombre_usuario; ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($perfil['email']); ?></small>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/perfil/ver.php?id=<?php echo $_SESSION['usuario_id']; ?>">
                                <i class="bi bi-person me-2"></i> Mi Perfil
                            </a>
                            <a class="dropdown-item" href="/configuracion/perfil.php">
                                <i class="bi bi-gear me-2"></i> Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../crearCuenta.html">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Manejar el cambio de color del ícono de búsqueda
        const searchInput = document.querySelector('.search-form .form-control');
        const searchIcon = document.querySelector('.search-form .bi-search');

        searchInput.addEventListener('focus', () => {
            searchIcon.style.color = '#6c757d';
        });

        searchInput.addEventListener('blur', () => {
            searchIcon.style.color = 'rgba(255, 255, 255, 0.7)';
        });
    });
</script>