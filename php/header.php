<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    // Consulta para obtener la foto de perfil
    $query = "SELECT foto_perfil FROM perfiles WHERE usuario_id = ?";
    $stmt = mysqli_prepare($enlace, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $perfil = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);

    // Definir la ruta de la foto de perfil
    $foto_perfil = $perfil['foto_perfil'] ?? 'default-profile.jpg'; // Si no tiene foto, usa la predeterminada
} else {
    $foto_perfil = 'default-profile.jpg'; // Si no está logueado, usa la predeterminada
}
?>
<!-- Font Awesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Incluir solo Bootstrap de manera selectiva -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Incluir solo el archivo CSS de tu proyecto -->
<link rel="stylesheet" href="../CSS/estilosprin.css">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">
            <img src="../media/logoweb.svg" alt="Logo" class="img-fluid" style="max-height: 50px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">
                <!-- Formulario de búsqueda -->
                <li class="nav-item">
                    <form class="d-flex" action="busqueda.php" method="GET">
                        <input class="form-control me-2" type="search" name="query" placeholder="Buscar" aria-label="Buscar">
                        <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i> Buscar</button>
                    </form>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="ventas.php">
                        <i class="bi bi-cart-fill"></i> Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/ayuda-estudiantil">
                        <i class="bi bi-info-circle-fill"></i> Ayuda Estudiantil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="perfil.php">
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" class="rounded-circle" style="width: 40px; height: 40px;">
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>


<!-- Script de Bootstrap (lo movemos aquí para asegurar que cargue al final) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>