<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Página Principal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/estilosprin.css">
</head>

<body>
    <?php include('header.php'); ?>

    <!-- Main Content -->
    <main class="container mt-4">
        <h2 class="mb-4 fs-1">Más publicaciones</h2>

        <!-- Publicaciones destacadas -->
        <div class="row mb-4 publicaciones bg-light p-4 rounded-3 shadow-sm">
            <div class="col-12">
                <div class="content-item d-flex gap-3 align-items-center">
                    <div style="flex: 0 0 60%; height: 200px;">
                        <img src="../media/user_icon_001.jpg"
                            class="img-fluid rounded-3 border border-dark"
                            alt="Imagen de contenido"
                            style="object-fit: cover; height: 100%; width: 100%;">
                    </div>
                    <div>
                        <a href="publicaciones.php" class="btn btn-primary mt-3">Ver más</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mb-4 fs-3">Lo más reciente...</h2>

        <!-- Lista de publicaciones -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="../media/logoweb.jpg" class="card-img-top" alt="Imagen de publicación">
                        <div class="card-body">
                            <h5 class="card-title">Título de la publicación</h5>
                            <p class="card-text">Resumen de la publicación</p>
                            <a href="#" class="btn btn-outline-primary">Leer más</a>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="row mt-4">
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.google.com/maps/place/Centro+de+Estudios+Cient%C3%ADficos+y+Tecnol%C3%B3gicos+N%C2%B0+3+%E2%80%9CEstanislao+Ram%C3%ADrez+Ruiz%E2%80%9D+IPN/@19.5707461,-99.021819,17z"
                    class="btn btn-secondary w-100" target="_blank">Ubicación</a>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.cecyt3.ipn.mx/index.html#technicalCareers"
                    class="btn btn-secondary w-100" target="_blank">Oferta educativa</a>
            </div>
            <div class="col-12 col-md-4 mb-2">
                <a href="https://www.saes.cecyt3.ipn.mx/"
                    class="btn btn-secondary w-100" target="_blank">SAES</a>
            </div>
        </div>
    </main>

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
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>