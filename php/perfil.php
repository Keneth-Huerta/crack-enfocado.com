<?php
// Iniciar sesión de forma segura
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';

// Verificar conexión
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario
$usuario_ids = isset($_GET['usuario_id']) && !empty($_GET['usuario_id']) ? (int) $_GET['usuario_id'] : $_SESSION['usuario_id'];

// Consultar el perfil del usuario con la tabla y columnas que describiste
$query = "
    SELECT perfiles.nombre, perfiles.apellido, perfiles.carrera, perfiles.semestre, 
           perfiles.foto_perfil, perfiles.foto_portada, perfiles.informacion_extra, 
           usuarios.username, usuarios.correo, usuarios.boleta, usuarios.fecha_registro
    FROM perfiles
    JOIN usuarios ON perfiles.usuario_id = usuarios.id
    WHERE perfiles.usuario_id = ?";

$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_ids);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Validar si el perfil fue encontrado
if (!$perfil) {
    die("El perfil no fue encontrado o no existe.");
}

// Consultar publicaciones del usuario
$publicaciones_query = "SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicada DESC";
$stmt_pub = mysqli_prepare($enlace, $publicaciones_query);
mysqli_stmt_bind_param($stmt_pub, "i", $usuario_ids);
mysqli_stmt_execute($stmt_pub);
$publicaciones_result = mysqli_stmt_get_result($stmt_pub);

// Consultar ventas del usuario con la columna `id` corregida
$ventas_query = "SELECT * FROM productos WHERE usuario_id = ? ORDER BY id DESC";
$stmt_ventas = mysqli_prepare($enlace, $ventas_query);
mysqli_stmt_bind_param($stmt_ventas, "i", $usuario_ids);
mysqli_stmt_execute($stmt_ventas);
$ventas_result = mysqli_stmt_get_result($stmt_ventas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($perfil['nombre'] ?? 'Usuario Desconocido') . ' ' . htmlspecialchars($perfil['apellido'] ?? ''); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h1>Perfil de <?php echo htmlspecialchars($perfil['nombre'] ?? 'Usuario Desconocido') . ' ' . htmlspecialchars($perfil['apellido'] ?? ''); ?></h1>

        <!-- Foto de perfil -->
        <img src="<?php echo htmlspecialchars($perfil['foto_perfil'] ?? 'default.png'); ?>" alt="Foto de perfil" class="rounded-circle" width="150">

        <!-- Información del perfil -->
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($perfil['correo']); ?></p>
        <p><strong>Boleta:</strong> <?php echo htmlspecialchars($perfil['boleta']); ?></p>
        <p><strong>Carrera:</strong> <?php echo htmlspecialchars($perfil['carrera']); ?></p>
        <p><strong>Semestre:</strong> <?php echo htmlspecialchars($perfil['semestre']); ?></p>
        <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($perfil['fecha_registro']); ?></p>

        <ul class="nav nav-tabs mt-4" id="perfilTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="publicaciones-tab" data-bs-toggle="tab" data-bs-target="#publicaciones" type="button" role="tab">Publicaciones</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">Ventas</button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="perfilTabsContent">
            <!-- Pestaña de publicaciones -->
            <div class="tab-pane fade show active" id="publicaciones" role="tabpanel">
                <?php if (mysqli_num_rows($publicaciones_result) > 0): ?>
                    <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)) { ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                <?php else: ?>
                    <p>No se encontraron publicaciones.</p>
                <?php endif; ?>
            </div>

            <!-- Pestaña de ventas -->
            <div class="tab-pane fade" id="ventas" role="tabpanel">
                <?php if (mysqli_num_rows($ventas_result) > 0): ?>
                    <?php while ($venta = mysqli_fetch_assoc($ventas_result)) { ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <p><strong>Producto:</strong> <?php echo htmlspecialchars($venta['producto']); ?></p>
                                <p><strong>Precio:</strong> $<?php echo htmlspecialchars($venta['precio']); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                <?php else: ?>
                    <p>No se encontraron ventas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Cerrar conexiones solo si están abiertas
if ($stmt) mysqli_stmt_close($stmt);
if ($stmt_pub) mysqli_stmt_close($stmt_pub);
if ($stmt_ventas) mysqli_stmt_close($stmt_ventas);
mysqli_close($enlace);
?>