<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Página Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .carousel-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black;
            border-radius: 50%;
            padding: 3px;
        }

        .btn-ver-todas {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h2>Publicaciones Recientes</h2>
        <div id="publicacionesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                // Iniciar sesión
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                require_once 'conexion.php';

                $stmt = $enlace->prepare("SELECT imagen, contenido, fecha_publicada FROM publicaciones ORDER BY fecha_publicada DESC LIMIT 5");
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows > 0) {
                    $first = true;
                    while ($publicacion = $resultado->fetch_assoc()) {
                        $activeClass = $first ? "active" : "";
                        $first = false;
                ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <img src="<?php echo htmlspecialchars($publicacion['imagen'] ?? 'https://via.placeholder.com/300'); ?>" class="d-block w-100" alt="Publicación">
                                </div>
                                <div class="col-12 col-md-8">
                                    <h5><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></h5>
                                    <p><small>Publicado el <?php echo date("d/m/Y H:i", strtotime($publicacion['fecha_publicada'])); ?></small></p>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No hay publicaciones recientes.</p>";
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

        <div class="text-center mt-4">
            <a href="publicaciones.php" class="btn btn-primary">Ver todas las publicaciones</a>
        </div>

        <footer class="container-fluid bg-light mt-4 py-3 text-center">
            <p>&copy; 2024 Centro de Estudios Científicos y Tecnológicos No. 3 "Estanislao Ramírez Ruiz".</p>
            <p>Contacto: <a href="mailto:info@cecyt3.ipn.mx">info@cecyt3.ipn.mx</a></p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>