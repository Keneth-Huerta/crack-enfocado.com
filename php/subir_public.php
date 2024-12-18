<?php
include 'basePublicacion.php'; // Archivo de conexión a la base de datos
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.html"); // Redirige al login si no está autenticado
    exit;
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['contenido']);
    $username = $_SESSION['usuario'];
    $imagePath = null;

    // Validar contenido
    if (empty($content) && empty($_FILES['image']['name'])) {
        echo "No puedes publicar un mensaje vacío.";
        exit;
    }

    // Procesar la imagen (si existe)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../media/'; // Carpeta para guardar las imágenes
    

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        // Mover el archivo subido a la carpeta de destino
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    }

    // Guardar la publicación en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO publicaciones (usuario, contenido, imagen) VALUES (:username, :content, :imagen)");
        $stmt->execute([
            ':username' => $username,
            ':content' => $content,
            ':imagen' => $imagePath
        ]);

        // Redirigir al usuario a la página principal tras publicar
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al guardar la publicación: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
    exit;
}
?>
