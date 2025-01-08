<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $query = "SELECT foto_perfil FROM perfiles WHERE usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $perfil = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    $foto_perfil = $perfil['foto_perfil'] ?? 'default-profile.jpg';
} else {
    $foto_perfil = 'default-profile.jpg';
}
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS para fijar el header -->
<style>
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1050;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    body {
        padding-top: 70px;
        /* Ajusta el padding para evitar que el contenido quede oculto detrás de la barra */
    }
</style>

<!-- Navbar Fijo en la parte superior -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #952F57;">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">
            <img src="../media/logoweb.svg" alt="Logo" class="img-fluid" style="max-height: 50px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido de la barra -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <!-- Formulario de búsqueda -->
                <li class="nav-item">
                    <form class="d-flex" action="busqueda.php" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Buscar" aria-label="Buscar">
                        <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i> Buscar</button>
                    </form>
                </li>

                <!-- Enlace a ventas -->
                <li class="nav-item">
                    <a class="nav-link" href="ventas.php">Ventas</a>
                </li>

                <!-- Perfil del usuario -->
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>"
                            alt="Foto de perfil"
                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>