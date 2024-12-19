// dar_like.php
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "error|No autorizado";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_publicacion = $_POST['id_publicacion'];
    
    // Verificar si ya existe el like
    $stmt = $enlace->prepare("SELECT * FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
    $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Insertar like
        $stmt = $enlace->prepare("INSERT INTO likes (usuario_id, publicacion_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();
        
        // Actualizar contador
        $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta + 1 WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        
        // Obtener nuevo contador
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo "liked|" . $row['cantidad_megusta'];
    } else {
        // Eliminar like
        $stmt = $enlace->prepare("DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $id_publicacion);
        $stmt->execute();
        
        // Actualizar contador
        $stmt = $enlace->prepare("UPDATE publicaciones SET cantidad_megusta = cantidad_megusta - 1 WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        
        // Obtener nuevo contador
        $stmt = $enlace->prepare("SELECT cantidad_megusta FROM publicaciones WHERE id_publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        echo "unliked|" . $row['cantidad_megusta'];
    }
}
?>