
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
    <!-- Meta tags básicos -->
    <meta name="description" content="Red Social Académica del CECyT 3 'Estanislao Ramírez Ruiz'. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="keywords" content="CECyT 3, IPN, red social académica, estudiantes, materiales escolares">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta property="og:title" content="CECyT 3 - Red Social Académica">
    <meta property="og:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta property="og:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">
    <meta property="og:image:alt" content="CECyT 3 Red Social Académica">
    <meta property="og:site_name" content="CECyT 3">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://steelblue-pelican-262083.hostingersite.com/">
    <meta name="twitter:title" content="CECyT 3 - Red Social Académica">
    <meta name="twitter:description" content="Red Social Académica del CECyT 3. Conecta con compañeros, comparte materiales y conocimiento.">
    <meta name="twitter:image" content="https://steelblue-pelican-262083.hostingersite.com/media/Crack-Enfocado.png">

    <!-- WhatsApp -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="CECyT 3">

    <!-- Android -->
    <meta name="theme-color" content="#741931">
    <link rel="manifest" href="/manifest.json">

    <!-- Favicon y íconos -->
    <link rel="mask-icon" href="/media/safari-pinned-tab.svg" color="#741931">
    <link rel="shortcut icon" href="/media/logoweb.svg" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Página Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ipn-vino: #952F57;
            --ipn-guinda: #741739;
            --ipn-dorado: #C4983D;
            --ipn-gris: #58595B;
            --ipn-blanco: #FFFFFF;
        }

        body {
            padding-top: 70px;
            font-family: 'Roboto', sans-serif;
            color: var(--ipn-gris);
        }

        .navbar {
            background: linear-gradient(to right, var(--ipn-guinda), var(--ipn-vino));
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
        }

        .navbar-brand img {
            height: 50px;
        }

        .nav-link {
            color: var(--ipn-blanco);
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--ipn-dorado);
            border-bottom-color: var(--ipn-dorado);
        }

        .section-title {
            color: var(--ipn-guinda);
            border-bottom: 3px solid var(--ipn-dorado);
        }

        .product-card {
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .price {
            color: var(--ipn-vino);
        }

        .btn-ver-todas {
            background-color: var(--ipn-vino);
            color: var(--ipn-blanco);
            transition: all 0.3s ease;
        }

        .btn-ver-todas:hover {
            background-color: var(--ipn-guinda);
            color: var(--ipn-dorado);
            transform: translateY(-2px);
        }

        footer {
            background: linear-gradient(to right, var(--ipn-guinda), var(--ipn-vino));
            color: var(--ipn-blanco);
        }

        footer h4 {
            color: var(--ipn-dorado);
        }

        footer a {
            color: var(--ipn-dorado);
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--ipn-blanco);
        }
    </style>

</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <!-- Sección de Publicaciones -->
        <h2 class="section-title mb-4">Publicaciones Recientes</h2>
        <div id="publicacionesCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
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
                                        <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>"
                                            class="img-fluid w-100"
                                            style="height: 300px; object-fit: cover;"
                                            alt="Publicación">
                                    <?php else: ?>
                                        <img src="media/publicacion_default.jpg"
                                            class="img-fluid w-100"
                                            style="height: 300px; object-fit: cover;"
                                            alt="Imagen no disponible">
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="p-4">
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

        <h2 class="section-title mb-4">Productos Destacados</h2>
        <div id="ventasCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $stmt = $enlace->prepare("SELECT p.*, u.username 
                            FROM productos p 
                            JOIN usuarios u ON p.usuario_id = u.id 
                            ORDER BY p.idProducto DESC LIMIT 6");
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $first = true;
                    while ($producto = $resultado->fetch_assoc()) {
                        $activeClass = $first ? "active" : "";
                        $first = false;
                ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <div class="row g-0">
                                <div class="col-12 col-md-5">
                                    <?php if (!empty($producto['imagen'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                                            class="img-fluid w-100" style="height: 300px; object-fit: cover;" alt="Producto">
                                    <?php else: ?>
                                        <img src="media/producto_default.jpg"
                                            class="img-fluid w-100" style="height: 300px; object-fit: cover;" alt="Imagen no disponible">
                                    <?php endif; ?>
                                </div>
                                <div class="col-12 col-md-7">
                                    <div class="card-body p-4">
                                        <h5 class="card-title"><?php echo htmlspecialchars($producto['producto']); ?></h5>
                                        <p class="card-text price">$<?php echo number_format($producto['precio'], 2); ?></p>
                                        <p class="card-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                        <small class="text-muted d-block mb-3">Vendedor: <?php echo htmlspecialchars($producto['username']); ?></small>
                                        <a href="detalle_producto.php?id=<?php echo $producto['idProducto']; ?>"
                                            class="btn btn-ver-todas btn-sm">Ver detalles</a>
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

            <button class="carousel-control-prev" type="button" data-bs-target="#ventasCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#ventasCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <div class="text-center">
            <a href="publicaciones.php" class="btn btn-ver-todas me-3">Ver todas las publicaciones</a>
            <a href="ventas.php" class="btn btn-ver-todas">Ver todos los productos</a>
        </div>
    </div>

    <footer class="mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
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