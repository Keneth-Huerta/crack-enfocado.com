<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Página Principal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos adicionales para mejorar la apariencia del carrusel -->
    <style>
        /* Ajuste de las imágenes dentro del carrusel */
        .carousel-item img {
            width: 100%;
            /* Asegura que la imagen ocupe todo el ancho del contenedor */
            height: 300px;
            /* Altura fija para todas las imágenes */
            object-fit: cover;
            /* Asegura que la imagen cubra el área sin distorsionarse */
        }

        /* Personalización de las flechas del carrusel */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black;
            /* Cambia las flechas a color negro */
            border-radius: 50%;
            /* Hace las flechas redondas */
            padding: 3px;
        }

        .carousel-control-prev-icon:hover,
        .carousel-control-next-icon:hover {
            background-color: #333;
            /* Cambia el color al pasar el ratón por encima */
        }

        /* Estilo adicional para el contenido del carrusel */
        .carousel-item h5 {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .carousel-item p {
            color: #666;
            font-size: 0.9rem;
        }

        .carousel-item .row {
            padding: 10px;
        }

        /* Botón para ver todas las publicaciones */
        .btn-ver-todas {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
        }

        .btn-ver-todas:hover {
            background-color: #0056b3;
        }
    </style>
</head>


<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <!-- Carrusel de Publicaciones Recientes -->
        <h2>Publicaciones Recientes</h2>
        <div id="publicacionesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php

                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                require_once 'conexion.php';

                // Obtener las últimas 5 publicaciones
                $query = "SELECT * FROM publicaciones ORDER BY fecha_publicada DESC LIMIT 5";
                $resultado = mysqli_query($enlace, $query);

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $first = true;
                    while ($publicacion = mysqli_fetch_assoc($resultado)) {
                        $activeClass = $first ? "active" : "";
                        $first = false;
                ?>
                        <div class="carousel-item <?php echo $activeClass; ?>">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <?php if (!empty($publicacion['imagen'])): ?>
                                        <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" class="d-block w-100" alt="Publicación">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300" class="d-block w-100" alt="Publicación">
                                    <?php endif; ?>
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
                ?>
            </div>

            <!-- Controles del Carrusel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#publicacionesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#publicacionesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Enlace a la página de todas las publicaciones -->
        <div class="text-center mt-4">
            <a href="publicaciones.php" class="btn-ver-todas">Ver todas las publicaciones</a>
        </div>

        <div class="row mt-4">
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.google.com/maps/place/Centro+de+Estudios+Cient%C3%ADficos+y+Tecnol%C3%B3gicos+N%C2%B0+3+%E2%80%9CEstanislao+Ram%C3%ADrez+Ruiz%E2%80%9D+IPN/@19.5707461,-99.021819,17z"
                    class="btn btn-secondary w-100" target="_blank">Ubicación</a>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.cecyt3.ipn.mx/index.php#technicalCareers"
                    class="btn btn-secondary w-100" target="_blank">Oferta educativa</a>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.saes.cecyt3.ipn.mx/"
                    class="btn btn-secondary w-100" target="_blank">SAES</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="container-fluid bg-light mt-4 py-3">
        <div class="text-center">
            <p>&copy; 2024 Centro de Estudios Científicos y Tecnológicos No. 3 "Estanislao Ramírez Ruiz". Todos los derechos reservados.</p>
            <div class="social-links mb-2">
                <a href="https://www.facebook.com" target="_blank" class="text-decoration-none me-2">Facebook</a>
                <a href="https://www.twitter.com" target="_blank" class="text-decoration-none me-2">Twitter</a>
                <a href="https://www.instagram.com" target="_blank" class="text-decoration-none">Instagram</a>
            </div>
            <p>Contacto: <a href="mailto:info@cecyt3.ipn.mx">info@cecyt3.ipn.mx</a></p>
        </div>
    </footer>


<!-- Bootstrap JS (incluye Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>