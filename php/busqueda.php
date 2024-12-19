<?php
require_once 'conexion.php'; // Asegúrate de tener la conexión correcta

// Obtener el término de búsqueda
$buscar = isset($_GET['buscar']) ? mysqli_real_escape_string($enlace, $_GET['buscar']) : '';

if ($buscar) {
    // Consulta de búsqueda para usuarios
    $query_usuarios = "SELECT * FROM usuarios WHERE 
                        username LIKE ? OR 
                        correo LIKE ? OR 
                        nombre LIKE ? OR 
                        apellido LIKE ? OR 
                        carrera LIKE ? OR 
                        semestre LIKE ?";
    $stmt_usuarios = mysqli_prepare($enlace, $query_usuarios);
    $search_term = "%" . $buscar . "%";  // Para búsqueda parcial
    mysqli_stmt_bind_param($stmt_usuarios, "sssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt_usuarios);
    $resultado_usuarios = mysqli_stmt_get_result($stmt_usuarios);

    // Consulta de búsqueda para publicaciones
    $query_publicaciones = "SELECT * FROM publicaciones WHERE contenido LIKE ? OR 
                            fecha_publicada LIKE ?";
    $stmt_publicaciones = mysqli_prepare($enlace, $query_publicaciones);
    mysqli_stmt_bind_param($stmt_publicaciones, "ss", $search_term, $search_term);
    mysqli_stmt_execute($stmt_publicaciones);
    $resultado_publicaciones = mysqli_stmt_get_result($stmt_publicaciones);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <!-- Campo de búsqueda -->
        <form method="get" action="busqueda.php">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="buscar" placeholder="Buscar usuarios o publicaciones" value="<?php echo htmlspecialchars($buscar); ?>">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
            </div>
        </form>

        <!-- Resultados de usuarios -->
        <h3>Usuarios</h3>
        <ul class="list-group">
            <?php if ($resultado_usuarios && mysqli_num_rows($resultado_usuarios) > 0): ?>
                <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido']); ?></strong> - 
                        <?php echo htmlspecialchars($usuario['username']); ?> - 
                        <?php echo htmlspecialchars($usuario['correo']); ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item">No se encontraron usuarios.</li>
            <?php endif; ?>
        </ul>

        <!-- Resultados de publicaciones -->
        <h3 class="mt-4">Publicaciones</h3>
        <ul class="list-group">
            <?php if ($resultado_publicaciones && mysqli_num_rows($resultado_publicaciones) > 0): ?>
                <?php while ($publicacion = mysqli_fetch_assoc($resultado_publicaciones)): ?>
                    <li class="list-group-item">
                        <strong><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></strong><br>
                        <small>Publicado el <?php echo date("d/m/Y H:i", strtotime($publicacion['fecha_publicada'])); ?></small>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li class="list-group-item">No se encontraron publicaciones.</li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
