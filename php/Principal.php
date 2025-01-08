<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Página Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <!-- Sección de Publicaciones -->
        <h2 class="section-title">Publicaciones Recientes</h2>
        <div id="publicacionesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $stmt = $enlace->prepare("SELECT id, imagen, contenido, fecha_publicada FROM publicaciones ORDER BY fecha_publicada DESC LIMIT 5");
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $first = true;
                    while ($publicacion = $resultado->fetch_assoc()) {
                        $activeClass = $first ? "active" : "";
                        $first = false;
                ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <div class="row g-0">
                                <div class="col-12 col-md-4">
                                    <?php if (!empty($publicacion['imagen'])): ?>
                                        <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" class="img-fluid" alt="Publicación">
                                    <?php else: ?>
                                        <img src="media/publicacion_default.jpg" class="img-fluid" alt="Imagen no disponible">
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="carousel-content">
                                        <div class="pe-4">
                                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                                            <small class="text-muted d-block mb-2">
                                                Publicado el <?php echo date("d/m/Y H:i", strtotime($publicacion['fecha_publicada'])); ?>
                                            </small>
                                            <a href="detalles_publicacion.php?id=<?php echo $publicacion['id']; ?>"
                                                class="btn btn-ver-todas btn-sm">Ver más</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                $stmt->close();
                ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#publicacionesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#publicacionesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Sección de Ventas -->
        <h2 class="section-title">Productos Destacados</h2>
        <div id="ventasCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $stmt = $enlace->prepare("SELECT p.*, u.username 
                                        FROM productos p 
                                        JOIN usuarios u ON p.usuario_id = u.id 
                                        ORDER BY p.idProducto DESC LIMIT 6");
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $productos = array();
                    while ($producto = $resultado->fetch_assoc()) {
                        $productos[] = $producto;
                    }

                    for ($i = 0; $i < count($productos); $i += 2) {
                        $activeClass = $i === 0 ? "active" : "";
                ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <div class="row">
                                <?php for ($j = $i; $j < min($i + 2, count($productos)); $j++) { ?>
                                    <div class="col-md-6">
                                        <div class="product-card">
                                            <?php if (!empty($productos[$j]['imagen'])): ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($productos[$j]['imagen']); ?>"
                                                    class="w-100 product-image" alt="Producto">
                                            <?php else: ?>
                                                <img src="media/producto_default.jpg"
                                                    class="w-100 product-image" alt="Imagen no disponible">
                                            <?php endif; ?>
                                            <div class="product-details">
                                                <h5><?php echo htmlspecialchars($productos[$j]['producto']); ?></h5>
                                                <p class="price">$<?php echo number_format($productos[$j]['precio'], 2); ?></p>
                                                <p class="description"><?php echo htmlspecialchars($productos[$j]['descripcion']); ?></p>
                                                <small class="text-muted">Vendedor: <?php echo htmlspecialchars($productos[$j]['username']); ?></small>
                                                <div class="mt-2">
                                                    <a href="detalles_producto.php?id=<?php echo $productos[$j]['idProducto']; ?>"
                                                        class="btn btn-ver-todas btn-sm">Ver detalles</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php
                    }
                }
                $stmt->close();
                ?>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#ventasCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#ventasCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <div class="text-center mt-4">
            <a href="publicaciones.php" class="btn btn-ver-todas me-3">Ver todas las publicaciones</a>
            <a href="ventas.php" class="btn btn-ver-todas">Ver todos los productos</a>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <h4>CECyT 3 "Estanislao Ramírez Ruiz"</h4>
                    <p>Centro de Estudios Científicos y Tecnológicos</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p>Contacto: <a href="mailto:info@cecyt3.ipn.mx">info@cecyt3.ipn.mx</a></p>
                    <p>&copy; 2024 Instituto Politécnico Nacional</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>