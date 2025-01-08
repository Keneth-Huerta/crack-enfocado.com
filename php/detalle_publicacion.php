<?php
session_start();
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $publicacionId = $_GET['id'];

    $stmt = $enlace->prepare("SELECT * FROM publicaciones WHERE id_publicacion = ?");
    $stmt->bind_param("i", $publicacionId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $publicacion = $resultado->fetch_assoc();
    } else {
        echo "Publicación no encontrada.";
        exit();
    }
} else {
    echo "ID de publicación no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la publicación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Agrega tus estilos CSS aquí */
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h2>Detalles de la publicación</h2>
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($publicacion['imagen'] ?? 'https://via.placeholder.com/300'); ?>" class="img-fluid" alt="Publicación">
            </div>
            <div class="col-md-6">
                <p class="mb-3"><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                <small class="text-muted">
                    Publicado el <?php echo date("d/m/Y H:i", strtotime($publicacion['fecha_publicada'])); ?>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>