<?php
// Iniciar sesión solo si aún no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once 'conexion.php';

// Verificar conexión a la base de datos
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión activa con validación más robusta
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Depuración: Mostrar las variables de sesión (puedes eliminar esto después de probar)
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Obtener el ID del usuario desde la URL o sesión
if (isset($_GET['usuario_id']) && !empty($_GET['usuario_id'])) {
    $usuario_ids = (int) $_GET['usuario_id'];
} else {
    $usuario_ids = $_SESSION['usuario_id'];
}

// Preparar y ejecutar la consulta para obtener el perfil del usuario
$query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_ids);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Validación adicional en caso de que el perfil no se encuentre
if (!$perfil) {
    die("Error: El perfil no existe o no se pudo cargar.");
}

// Preparar y ejecutar la consulta para obtener publicaciones del usuario
$publicaciones_query = "SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicada DESC";
$stmt_pub = mysqli_prepare($enlace, $publicaciones_query);
mysqli_stmt_bind_param($stmt_pub, "i", $usuario_ids);
mysqli_stmt_execute($stmt_pub);
$publicaciones_result = mysqli_stmt_get_result($stmt_pub);

// Preparar y ejecutar la consulta para obtener ventas del usuario
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
    <title>Perfil de <?php echo htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h1>Perfil de <?php echo htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']); ?></h1>
        <img src="<?php echo htmlspecialchars($perfil['foto_perfil']); ?>" alt="Foto de perfil" class="rounded-circle" width="150">

        <!-- Navegación de pestañas -->
        <ul class="nav nav-tabs mt-4" id="perfilTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="publicaciones-tab" data-bs-toggle="tab" data-bs-target="#publicaciones" type="button" role="tab">Publicaciones</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">Ventas</button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
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
                    <p>No hay publicaciones disponibles.</p>
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
                    <p>No hay ventas disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Cerrar todas las declaraciones y la conexión a la base de datos
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_pub);
mysqli_stmt_close($stmt_ventas);
mysqli_close($enlace);
?>