<?php
require_once 'conexion.php';

// Obtener el término de búsqueda
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Si hay un término de búsqueda, realizar la búsqueda
if (!empty($query)) {
    // Buscar en la tabla de publicaciones
    $query_publicaciones = "SELECT * FROM publicaciones WHERE contenido LIKE ? LIMIT 5";
    $stmt_publicaciones = mysqli_prepare($enlace, $query_publicaciones);
    $search_term = "%" . $query . "%";
    mysqli_stmt_bind_param($stmt_publicaciones, "s", $search_term);
    mysqli_stmt_execute($stmt_publicaciones);
    $resultado_publicaciones = mysqli_stmt_get_result($stmt_publicaciones);
    mysqli_stmt_close($stmt_publicaciones);

    // Buscar en la tabla de usuarios (con nombres de columnas corregidos)
    $query_usuarios = "SELECT * FROM usuarios WHERE usuario_nombre LIKE ? OR usuario_apellido LIKE ? LIMIT 5"; // Asegúrate de que 'usuario_nombre' y 'usuario_apellido' son los nombres correctos
    $stmt_usuarios = mysqli_prepare($enlace, $query_usuarios);
    mysqli_stmt_bind_param($stmt_usuarios, "ss", $search_term, $search_term);
    mysqli_stmt_execute($stmt_usuarios);
    $resultado_usuarios = mysqli_stmt_get_result($stmt_usuarios);
    mysqli_stmt_close($stmt_usuarios);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h2>Resultados de búsqueda para "<?php echo htmlspecialchars($query); ?>"</h2>

        <?php if (!empty($query)): ?>
            <h4>Publicaciones:</h4>
            <?php if (mysqli_num_rows($resultado_publicaciones) > 0): ?>
                <ul class="list-group">
                    <?php while ($publicacion = mysqli_fetch_assoc($resultado_publicaciones)): ?>
                        <li class="list-group-item">
                            <a href="publicacion.php?id=<?php echo $publicacion['id_publicacion']; ?>">
                                <?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No se encontraron publicaciones.</p>
            <?php endif; ?>

            <hr>

            <h4>Usuarios:</h4>
            <?php if (mysqli_num_rows($resultado_usuarios) > 0): ?>
                <ul class="list-group">
                    <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                        <li class="list-group-item">
                            <a href="perfil.php?id=<?php echo $usuario['usuario_id']; ?>">
                                <?php echo htmlspecialchars($usuario['usuario_nombre']) . ' ' . htmlspecialchars($usuario['usuario_apellido']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No se encontraron usuarios.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Por favor ingresa un término de búsqueda.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>