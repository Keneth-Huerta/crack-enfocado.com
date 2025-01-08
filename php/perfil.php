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
    // Si no se pasa el ID de usuario, redirigir al perfil del usuario actual
    $usuario_ids = $_SESSION['usuario_id'];
}
$perfil = [];
$publicaciones = [];
// Obtener productos del usuario
try {
    $productos_query = "SELECT * FROM productos WHERE usuario_id = ? ORDER BY idProducto DESC";

    if ($stmt_productos = mysqli_prepare($enlace, $productos_query)) {
        mysqli_stmt_bind_param($stmt_productos, "i", $usuario_ids);

        if (!mysqli_stmt_execute($stmt_productos)) {
            throw new Exception("Error al obtener productos: " . mysqli_error($enlace));
        }

        $productos_result = mysqli_stmt_get_result($stmt_productos);
    } else {
        throw new Exception("Error en la preparación de la consulta de productos");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $productos_result = false;
}

// Obtener publicaciones del usuario con manejo de errores
try {
    $publicaciones_query = "SELECT id_publicacion, contenido, imagen, fecha_publicada, usuario_id 
                            FROM publicaciones 
                            WHERE usuario_id = ? 
                            ORDER BY fecha_publicada DESC";

    if ($stmt_publicaciones = mysqli_prepare($enlace, $publicaciones_query)) {
        mysqli_stmt_bind_param($stmt_publicaciones, "i", $usuario_ids);

        if (!mysqli_stmt_execute($stmt_publicaciones)) {
            throw new Exception("Error al obtener publicaciones: " . mysqli_error($enlace));
        }

        $publicaciones_result = mysqli_stmt_get_result($stmt_publicaciones);
    } else {
        throw new Exception("Error en la preparación de la consulta de publicaciones");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $publicaciones_result = false;
}

// Obtener perfil con manejo de errores mejorado
try {
    $query = "SELECT * FROM perfiles JOIN usuarios on perfiles.usuario_id = usuarios.id WHERE usuario_id = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $usuario_ids);

        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            $perfil = mysqli_fetch_assoc($resultado);
        } else {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_error($enlace));
        }
        mysqli_stmt_close($stmt);
    } else {
        throw new Exception("Error en la preparación de la consulta: " . mysqli_error($enlace));
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Valores predeterminados seguros
$username = $perfil['username'] ?? 'Usuario no disponible';
$nombre = $perfil['nombre'] ?? 'Nombre no disponible';
$apellido = $perfil['apellido'] ?? 'Apellido no disponible';
$carrera = $perfil['carrera'] ?? 'Carrera no disponible';
$semestre = $perfil['semestre'] ?? 'Semestre no disponible';
$informacion_extra = $perfil['informacion_extra'] ?? 'No disponible';
$foto_perfils = $perfil['foto_perfil'] ?? '../media/user.png';
$foto_portada = $perfil['foto_portada'] ?? '../media/user_icon_001.jpg';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo htmlspecialchars($username); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/misestilos.css">

    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .nav-link {
            color: #000 !important;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="perfil-container">
        <!-- Foto de portada -->
        <div class="foto-portada">
            <img src="<?php echo htmlspecialchars($foto_portada); ?>" alt="Foto de portada">
        </div>

        <h1 class="titulo-perfil"><?php echo htmlspecialchars($username); ?></h1>

        <div class="perfil-info">
            <!-- Foto de perfil -->
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($foto_perfils); ?>" alt="Foto de perfil">
            </div>

            <div class="informacion">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($carrera); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($semestre); ?></p>
                <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($informacion_extra)); ?></p>
            </div>
        </div>
        <?php if ($usuario_ids == $_SESSION['usuario_id']): ?>

            <link rel="stylesheet" href="../css/misestilos.css">
            <div class="acciones">
                <a href="editar_perfil.php" class="btn-editar">
                    <i class="fas fa-edit"></i> Editar perfil
                </a>
                <a href="logout.php" class="btn-cerrar-sesion">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        <?php endif; ?>

        <!-- Sistema de pestañas -->
        <div class="container mt-4">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="publicaciones-tab" data-bs-toggle="tab"
                        data-bs-target="#publicaciones" type="button" role="tab">
                        <i class="far fa-newspaper"></i> Publicaciones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="productos-tab" data-bs-toggle="tab"
                        data-bs-target="#productos" type="button" role="tab">
                        <i class="fas fa-shopping-cart"></i> Productos en Venta
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <!-- Pestaña de Publicaciones -->
                <div class="tab-pane fade show active" id="publicaciones" role="tabpanel">
                    <div class="publicaciones-usuario">
                        <h2>Publicaciones</h2>
                        <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                            <div class="lista-publicaciones">
                                <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
                                    <div class="publicacion-item">
                                        <!-- Fecha y estadísticas -->
                                        <div class="publicacion-meta">
                                            <p class="fecha">
                                                <i class="far fa-clock"></i>
                                                <?php
                                                $fecha = new DateTime($publicacion['fecha_publicada']);
                                                echo $fecha->format('d/m/Y H:i');
                                                ?>
                                            </p>
                                        </div>

                                        <!-- Contenido de la publicación -->
                                        <div class="publicacion-contenido">
                                            <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                                            <?php if (!empty($publicacion['imagen'])): ?>
                                                <div class="publicacion-imagen">
                                                    <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>"
                                                        alt="Imagen de publicación">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Botones de acción -->
                                        <?php if ($usuario_ids == $_SESSION['usuario_id']): ?>
                                            <div class="acciones-publicacion">
                                                <a href="editar_publicacion.php?id=<?php echo $publicacion['id_publicacion']; ?>"
                                                    class="btn-editar">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <a href="eliminar_publicacion.php?id=<?php echo $publicacion['id_publicacion']; ?>"
                                                    class="btn-eliminar"
                                                    onclick="return confirm('¿Estás seguro de que deseas eliminar esta publicación?')">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="sin-publicaciones">
                                <p><i class="far fa-newspaper"></i> Aún no has realizado ninguna publicación.</p>
                                <a href="publicaciones.php" class="btn-nueva-publicacion">
                                    <i class="fas fa-plus"></i> Crear primera publicación
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pestaña de Productos -->
                <div class="tab-pane fade" id="productos" role="tabpanel">
                    <div class="productos-usuario container-fluid py-4">
                        <h2 class="text-center mb-4">Productos en Venta</h2>
                        <?php if ($productos_result && mysqli_num_rows($productos_result) > 0): ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <?php while ($producto = mysqli_fetch_assoc($productos_result)): ?>
                                    <div class="col">
                                        <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                                            <div class="position-relative">
                                                <?php if (!empty($producto['imagen'])): ?>
                                                    <img class="card-img-top object-fit-cover"
                                                        style="height: 250px;"
                                                        src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                                                        alt="Imagen del producto">
                                                <?php else: ?>
                                                    <img class="card-img-top object-fit-cover"
                                                        style="height: 250px;"
                                                        src="../media/producto_default.jpg"
                                                        alt="Imagen no disponible">
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <h3 class="card-title h5 mb-3"><?php echo htmlspecialchars($producto['producto']); ?></h3>
                                                <p class="card-text fw-bold text-primary fs-4 mb-2">
                                                    $<?php echo number_format($producto['precio'], 2); ?>
                                                </p>
                                                <p class="card-text text-secondary mb-3">
                                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                                </p>

                                                <?php if (isset($_SESSION['usuario_id'])): ?>
                                                    <?php if ($_SESSION['usuario_id'] == $producto['usuario_id']): ?>
                                                        <div class="mt-auto d-flex gap-2">
                                                            <a href="editar_producto.php?id=<?php echo $producto['idProducto']; ?>"
                                                                class="btn btn-outline-primary btn-sm flex-grow-1">
                                                                <i class="fas fa-edit me-1"></i> Editar
                                                            </a>
                                                            <a href="eliminar_producto.php?id=<?php echo $producto['idProducto']; ?>"
                                                                class="btn btn-outline-danger btn-sm flex-grow-1"
                                                                onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                                                <i class="fas fa-trash-alt me-1"></i> Eliminar
                                                            </a>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php
                                                        $telefono = $perfil['telefono'];
                                                        $mensaje = "Hola, me interesa tu producto: " . $producto['producto'] . " por $" . $producto['precio'];
                                                        $mensaje_codificado = urlencode($mensaje);
                                                        $whatsapp_link = "https://wa.me/{$telefono}?text={$mensaje_codificado}";
                                                        ?>
                                                        <div class="mt-auto">
                                                            <a href="<?php echo $whatsapp_link; ?>"
                                                                target="_blank"
                                                                class="btn btn-success w-100">
                                                                <i class="fab fa-whatsapp me-2"></i> Contactar por WhatsApp
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-store fs-1 text-secondary"></i>
                                </div>
                                <p class="text-secondary mb-4">Aún no tienes productos en venta.</p>
                                <a href="ventas.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Agregar primer producto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para lazy loading de imágenes
        document.addEventListener('DOMContentLoaded', function() {
            var lazyImages = [].slice.call(document.querySelectorAll("img[loading='lazy']"));

            if ("IntersectionObserver" in window) {
                let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            let lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.removeAttribute('loading');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }
        });
    </script>

    <!-- Bootstrap JS (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
if (isset($stmt_publicaciones)) {
    mysqli_stmt_close($stmt_publicaciones);
}
mysqli_close($enlace);
?>