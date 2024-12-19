<?php
session_start();
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
            $imagen_subida = 'uploads/' . basename($nueva_imagen);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicación</title>
    <link rel="stylesheet" href="../css/misestilos.css">
</head>
<body>
    <?php include('header.php'); ?>

    <div class="editar-publicacion">
        <h1>Editar Publicación</h1>

        <?php if (isset($error)): ?>
            <div class="error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <form action="editar_publicacion.php?id=<?php echo $publicacion_id; ?>" method="POST" enctype="multipart/form-data">
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="5" required><?php echo htmlspecialchars($publicacion['contenido']); ?></textarea>

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">

            <div class="acciones">
                <button type="submit" class="btn-editar">Guardar cambios</button>
                <a href="perfil.php" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
