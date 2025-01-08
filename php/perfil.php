<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

// Verificar conexión
if (!$enlace) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario desde la URL
if (isset($_GET['usuario_id'])) {
    $usuario_ids = (int) $_GET['usuario_id'];
} else {
    $usuario_ids = $_SESSION['usuario_id'];
}

// Consultar perfil del usuario
$query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_ids);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Consultar publicaciones del usuario
$publicaciones_query = "SELECT * FROM publicaciones WHERE usuario_id = ? ORDER BY fecha_publicada DESC";
$stmt_pub = mysqli_prepare($enlace, $publicaciones_query);
mysqli_stmt_bind_param($stmt_pub, "i", $usuario_ids);
mysqli_stmt_execute($stmt_pub);
$publicaciones_result = mysqli_stmt_get_result($stmt_pub);

// Consultar ventas del usuario
$ventas_query = "SELECT * FROM productos WHERE usuario_id = ? ORDER BY idProductos DESC";
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
                <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Pestaña de ventas -->
            <div class="tab-pane fade" id="ventas" role="tabpanel">
                <?php while ($venta = mysqli_fetch_assoc($ventas_result)) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <p><strong>Producto:</strong> <?php echo htmlspecialchars($venta['producto']); ?></p>
                            <p><strong>Precio:</strong> $<?php echo htmlspecialchars($venta['precio']); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_pub);
mysqli_stmt_close($stmt_ventas);
mysqli_close($enlace);
?>