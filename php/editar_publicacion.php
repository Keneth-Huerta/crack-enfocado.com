
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID de la publicación desde GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $publicacion_id = $_GET['id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Obtener los detalles de la publicación para editarla
    $query = "SELECT * FROM publicaciones WHERE id_publicacion = ? AND usuario_id = ?";
    if ($stmt = mysqli_prepare($enlace, $query)) {
        mysqli_stmt_bind_param($stmt, "ii", $publicacion_id, $usuario_id);

        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($resultado) > 0) {
                // La publicación existe, cargar los datos
                $publicacion = mysqli_fetch_assoc($resultado);
            } else {
                echo "No tienes permiso para editar esta publicación.";
                exit();
            }
        }
    }
} else {
    echo "ID de publicación no válido.";
    exit();
}

// Manejar la edición de la publicación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_contenido = $_POST['contenido'] ?? '';
    $nueva_imagen = $_FILES['imagen']['name'] ?? '';

    if (empty($nuevo_contenido)) {
        $error = "El contenido no puede estar vacío.";
    } else {
        // Subir la nueva imagen si se ha seleccionado una
        if (!empty($nueva_imagen)) {
            $imagen_subida = '../media/uploads/' . basename($nueva_imagen);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_subida);
        } else {
            $imagen_subida = $publicacion['imagen']; // Mantener la imagen actual
        }

        // Actualizar la publicación en la base de datos
        $update_query = "UPDATE publicaciones SET contenido = ?, imagen = ? WHERE id_publicacion = ? AND usuario_id = ?";
        if ($update_stmt = mysqli_prepare($enlace, $update_query)) {
            mysqli_stmt_bind_param($update_stmt, "ssii", $nuevo_contenido, $imagen_subida, $publicacion_id, $usuario_id);

            if (mysqli_stmt_execute($update_stmt)) {
                // Redirigir al perfil después de la edición
                header("Location: perfil.php?mensaje=Publicación actualizada con éxito");
                exit();
            } else {
                echo "Error al actualizar la publicación.";
            }
        }
    }
}
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
    <title>Editar Publicación</title>
    <link rel="stylesheet" href="../css/editarPublicacion.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container-edit-publication">
        <h1 class="form-title">Editar Publicación</h1>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form action="editar_publicacion.php?id=<?php echo $publicacion_id; ?>" method="POST" class="form-container" enctype="multipart/form-data">
            <label for="contenido" class="form-label">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="5" class="form-textarea" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea>

            <label for="imagen" class="form-label">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" class="form-input-file">

            <div class="action-buttons">
                <button type="submit" class="btn-save">Guardar cambios</button>
                <a href="perfil.php" class="btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (incluye Popper.js) -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
