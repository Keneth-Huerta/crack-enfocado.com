<?php
require_once 'conexion.php'; // Conexión a la base de datos

// Obtener el término de búsqueda
$searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '';

// Inicializar variables de resultado
$resultado_usuarios = null;
$resultado_publicaciones = null;

// Si se ha introducido un término de búsqueda, realizar las consultas
if ($searchTerm != '') {
    // Búsqueda de usuarios en la tabla perfiles
    $query_usuarios = "
        SELECT p.usuario_id, p.nombre, p.apellido, p.carrera, p.semestre, p.foto_perfil, u.username
        FROM perfiles p
        JOIN usuarios u ON p.usuario_id = u.id
        WHERE p.nombre LIKE ? OR p.apellido LIKE ? OR p.carrera LIKE ? OR p.semestre LIKE ? OR u.username LIKE ? OR u.correo LIKE ?
    ";

    $stmt_usuarios = mysqli_prepare($enlace, $query_usuarios);
    mysqli_stmt_bind_param($stmt_usuarios, 'ssssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt_usuarios);
    $resultado_usuarios = mysqli_stmt_get_result($stmt_usuarios);

    // Búsqueda de publicaciones
    $query_publicaciones = "SELECT * FROM publicaciones WHERE contenido LIKE ?";
    $stmt_publicaciones = mysqli_prepare($enlace, $query_publicaciones);
    mysqli_stmt_bind_param($stmt_publicaciones, 's', $searchTerm);
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

    <!-- Incluir Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir tu archivo CSS -->
    <link rel="stylesheet" href="../CSS/estilosprin.css">
</head>

<body>

    <div class="container mt-4">
        <h2>Resultados de búsqueda</h2>

        <!-- Formulario de búsqueda -->
        <form method="get" action="busqueda.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <!-- Resultados de búsqueda de usuarios -->
        <h3>Usuarios</h3>
        <?php if ($resultado_usuarios && mysqli_num_rows($resultado_usuarios) > 0): ?>
            <ul class="list-group">
                <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                    <li class="list-group-item d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" alt="Foto de perfil" class="rounded-circle" width="40" height="40">
                        <div class="ms-3">
                            <strong><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($usuario['username']); ?></span>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No se encontraron usuarios.</p>
        <?php endif; ?>

        <!-- Resultados de búsqueda de publicaciones -->
        <h3>Publicaciones</h3>
        <?php if ($resultado_publicaciones && mysqli_num_rows($resultado_publicaciones) > 0): ?>
            <ul class="list-group">
                <?php while ($publicacion = mysqli_fetch_assoc($resultado_publicaciones)): ?>
                    <li class="list-group-item">
                        <p><strong>Publicado el: <?php echo date("d/m/Y", strtotime($publicacion['fecha_publicada'])); ?></strong></p>
                        <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No se encontraron publicaciones.</p>
        <?php endif; ?>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
