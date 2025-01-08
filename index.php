<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CECyT 3 - Red Social Académica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --ipn-guinda: #741931;
            --ipn-dorado: #C4A657;
        }

        .navbar {
            background-color: var(--ipn-guinda);
        }

        .navbar-brand {
            color: white !important;
        }

        .nav-link {
            color: white !important;
        }

        .hero-section {
            background: linear-gradient(rgba(116, 25, 49, 0.9), rgba(116, 25, 49, 0.9)),
                url('media/cecyt9.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            min-height: 500px;
            display: flex;
            align-items: center;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--ipn-guinda);
            margin-bottom: 1rem;
        }

        .feature-card {
            border: none;
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-ipn {
            background-color: var(--ipn-dorado);
            color: white;
            border: none;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .btn-ipn:hover {
            background-color: var(--ipn-guinda);
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-ipn {
            border: 2px solid var(--ipn-dorado);
            color: var(--ipn-dorado);
            background: transparent;
            padding: 10px 25px;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .btn-outline-ipn:hover {
            background-color: var(--ipn-dorado);
            color: white;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background-color: var(--ipn-guinda);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
        }

        footer {
            background-color: var(--ipn-guinda);
            color: white;
        }

        .auth-buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CECyT 9</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-ipn" href="/secion.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-ipn" href="/crearCuenta.html">Crear Cuenta</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Red Social Académica CECyT 3</h1>
            <p class="lead mb-5">Conecta con compañeros, comparte materiales y conocimiento</p>
            <div class="auth-buttons">
                <a href="crearCuenta.html" class="btn btn-ipn btn-lg">Crear Cuenta</a>
                <a href="secion.php" class="btn btn-outline-ipn btn-lg">Iniciar Sesión</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Características Principales</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-user-graduate feature-icon"></i>
                            <h3 class="card-title">Perfiles Académicos</h3>
                            <p class="card-text">Crea tu perfil personalizado con tu información académica y conecta con otros estudiantes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-book feature-icon"></i>
                            <h3 class="card-title">Venta de Materiales</h3>
                            <p class="card-text">Compra y vende materiales escolares de forma segura dentro de la comunidad.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-comments feature-icon"></i>
                            <h3 class="card-title">Comunicación Directa</h3>
                            <p class="card-text">Contacta fácilmente con otros estudiantes a través de WhatsApp.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Use Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">¿Cómo Funciona?</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="step-number">1</div>
                        <h4>Regístrate</h4>
                        <p>Crea tu cuenta con tu correo institucional o personal</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="step-number">2</div>
                        <h4>Completa tu Perfil</h4>
                        <p>Añade tu información académica y foto</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="step-number">3</div>
                        <h4>Publica</h4>
                        <p>Comparte contenido o publica materiales</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="step-number">4</div>
                        <h4>Conecta</h4>
                        <p>Interactúa con otros estudiantes</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container text-center">
            <p class="mb-0">© 2024 CECyT 9 "Juan de Dios Bátiz" - IPN</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>