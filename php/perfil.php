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
$usuario_ids = isset($_GET['usuario_id']) ? (int) $_GET['usuario_id'] : $_SESSION['usuario_id'];

// Obtener perfil
$query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_ids);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);

// Obtener publicaciones
$publicaciones_query = "SELECT id_publicacion, contenido, imagen, fecha_publicada, usuario_id 
                        FROM publicaciones 
                        WHERE usuario_id = ? 
                        ORDER BY fecha_publicada DESC";
$stmt_publicaciones = mysqli_prepare($enlace, $publicaciones_query);
mysqli_stmt_bind_param($stmt_publicaciones, "i", $usuario_ids);
mysqli_stmt_execute($stmt_publicaciones);
$publicaciones_result = mysqli_stmt_get_result($stmt_publicaciones);

// Obtener ventas
$ventas_query = "SELECT idProducto, producto, precio, descripcion, imagen 
                 FROM productos 
                 WHERE usuario_id = ? 
                 ORDER BY idProducto DESC";
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
    <title>Perfil de <?php echo htmlspecialchars($perfil['username']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        .perfil-container {
            max-width: 900px;
            margin: 20px auto;
        }

        .perfil-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 50%;
            object-fit: cover;
        }

        .publicacion-img,
        .venta-img {
            max-width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }

        .tab-content {
            margin-top: 20px;
        }

        .publicacion-item,
        .venta-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container perfil-container">
        <!-- Foto de perfil y portada -->
        <div class="text-center">
            <img src="<?php echo htmlspecialchars($perfil['foto_portada']); ?>" alt="Foto de portada" class="img-fluid mb-3" style="width:100%; max-height: 300px; object-fit: cover;">
            <img src="<?php echo htmlspecialchars($perfil['foto_perfil']); ?>" alt="Foto de perfil" class="perfil-img">
            <h1 class="mt-3"><?php echo htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']); ?></h1>
            <p><strong>Carrera:</strong> <?php echo htmlspecialchars($perfil['carrera']); ?></p>
            <p><strong>Semestre:</strong> <?php echo htmlspecialchars($perfil['semestre']); ?></p>
            <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($perfil['informacion_extra'])); ?></p>
        </div>

        <!-- Pestañas de Bootstrap -->
        <ul class="nav nav-tabs mt-4" id="perfilTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="publicaciones-tab" data-bs-toggle="tab" data-bs-target="#publicaciones" type="button" role="tab">Publicaciones</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">Ventas</button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="perfilTabsContent">
            <!-- Pestaña de Publicaciones -->
            <div class="tab-pane fade show active" id="publicaciones" role="tabpanel">
                <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                    <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
                        <div class="publicacion-item">
                            <p><strong>Publicado el:</strong> <?php echo (new DateTime($publicacion['fecha_publicada']))->format('d/m/Y H:i'); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                            <?php if (!empty($publicacion['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de la publicación" class="publicacion-img">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay publicaciones disponibles.</p>
                <?php endif; ?>
            </div>

            <!-- Pestaña de Ventas -->
            <div class="tab-pane fade" id="ventas" role="tabpanel">
                <?php if ($ventas_result && mysqli_num_rows($ventas_result) > 0): ?>
                    <?php while ($venta = mysqli_fetch_assoc($ventas_result)): ?>
                        <div class="venta-item">
                            <h4><?php echo htmlspecialchars($venta['producto']); ?></h4>
                            <p><strong>Precio:</strong> $<?php echo htmlspecialchars($venta['precio']); ?></p>
                            <p><?php echo nl2br(htmlspecialchars($venta['descripcion'])); ?></p>
                            <?php if (!empty($venta['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($venta['imagen']); ?>" alt="Imagen del producto" class="venta-img">
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay productos en venta disponibles.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
// Cerrar conexiones
if (isset($stmt_publicaciones)) mysqli_stmt_close($stmt_publicaciones);
if (isset($stmt_ventas)) mysqli_stmt_close($stmt_ventas);
if (isset($stmt)) mysqli_stmt_close($stmt);
mysqli_close($enlace);
?>