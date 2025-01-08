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
        /* Variables de colores IPN */
        :root {
            --ipn-vino: #952F57;
            --ipn-guinda: #741739;
            --ipn-dorado: #C4983D;
            --ipn-gris: #58595B;
            --ipn-blanco: #FFFFFF;
        }

        /* Estilos generales */
        body {
            padding-top: 70px;
            font-family: 'Roboto', sans-serif;
            color: var(--ipn-gris);
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(to right, var(--ipn-guinda), var(--ipn-vino));
            padding: 0.7rem 1rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: var(--ipn-blanco) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--ipn-dorado) !important;
            border-bottom: 2px solid var(--ipn-dorado);
        }

        /* Carousel y tarjetas */
        .carousel {
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .carousel-item {
            padding: 1.5rem;
        }

        .section-title {
            color: var(--ipn-guinda);
            font-weight: 600;
            margin: 2rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--ipn-dorado);
        }

        .product-card {
            background: var(--ipn-blanco);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin: 1rem;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .product-details {
            padding: 1.5rem;
        }

        .price {
            color: var(--ipn-vino);
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Botones */
        .btn-ver-todas {
            background-color: var(--ipn-vino);
            color: var(--ipn-blanco);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-ver-todas:hover {
            background-color: var(--ipn-guinda);
            color: var(--ipn-dorado);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: linear-gradient(to right, var(--ipn-guinda), var(--ipn-vino));
            color: var(--ipn-blanco);
            padding: 2rem 0;
            margin-top: 3rem;
        }

        footer h4 {
            color: var(--ipn-dorado);
            margin-bottom: 1rem;
        }

        footer a {
            color: var(--ipn-dorado);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--ipn-blanco);
        }

        /* Formulario de búsqueda */
        .search-form .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--ipn-blanco);
        }

        .search-form .form-control:focus {
            background-color: var(--ipn-blanco);
            color: var(--ipn-gris);
            border-color: var(--ipn-dorado);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }

            .product-card {
                margin: 0.5rem;
            }

            .carousel-item {
                padding: 1rem;
            }
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
                $stmt = $enlace->prepare("SELECT id_publicacion, imagen, contenido, fecha_publicada FROM publicaciones ORDER BY fecha_publicada DESC LIMIT 5");
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
                                            <a href="detalle_publicacion.php?id=<?php echo $publicacion['id_publicacion']; ?>"
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
                            <div class="">
                                <?php for ($j = $i; $j < min($i+1, count($productos)); $j++) { ?>
                                    <div class="">
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
                                                    <a href="detalle_producto.php?id=<?php echo $productos[$j]['idProducto']; ?>"
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