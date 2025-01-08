<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// Función para manejar errores de base de datos
function handleDatabaseError($error)
{
    error_log("Error de base de datos: " . $error);
    return null;
}

// Obtener todas las notificaciones del usuario
function getAllUserNotifications($enlace, $usuario_id)
{
    try {
        $query = "SELECT n.*, 
                        CASE 
                            WHEN n.tipo = 'like' THEN CONCAT('/php/detalle_publicacion.php?id=', n.referencia_id)
                            WHEN n.tipo = 'comentario' THEN CONCAT('/php/detalle_publicacion.php?id=', n.referencia_id)
                            WHEN n.tipo = 'venta' THEN CONCAT('/php/ventas.php?id=', n.referencia_id)
                            ELSE '#'
                        END as enlace
                 FROM notificaciones n 
                 WHERE n.usuario_id = ?
                 ORDER BY n.fecha DESC";

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

// Marcar una notificación como leída
function markNotificationAsRead($enlace, $notificacion_id)
{
    try {
        $query = "UPDATE notificaciones SET leida = 1 WHERE id = ?";
        $stmt = mysqli_prepare($enlace, $query);
        mysqli_stmt_bind_param($stmt, "i", $notificacion_id);
        mysqli_stmt_execute($stmt);
    } catch (Exception $e) {
        handleDatabaseError($e->getMessage());
    }
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Obtener todas las notificaciones del usuario
$notificaciones = [];
if ($usuario_id) {
    $notificaciones = getAllUserNotifications($enlace, $usuario_id);
}

// Marcar una notificación como leída si se recibe el parámetro 'mark_read'
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    $notificacion_id = $_POST['notification_id'];
    markNotificationAsRead($enlace, $notificacion_id);
    // Redireccionar a la misma página para evitar el reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="/media/logoweb.svg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .notification-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container my-4">
        <h1>Notificaciones</h1>
        <?php if (empty($notificaciones)): ?>
            <div class="alert alert-info">No tienes notificaciones.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notificaciones as $notif): ?>
                    <form method="POST" class="notification-form">
                        <input type="hidden" name="notification_id" value="<?php echo $notif['id']; ?>">
                        <input type="hidden" name="mark_read" value="1">
                        <button type="submit" class="list-group-item list-group-item-action notification-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?php echo htmlspecialchars($notif['tipo']); ?></h5>
                                <small><?php echo date('d/m/Y H:i', strtotime($notif['fecha'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($notif['mensaje']); ?></p>
                            <?php if ($notif['leida'] == 0): ?>
                                <span class="badge bg-primary rounded-pill">Nueva</span>
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>