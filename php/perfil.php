<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");  // Si no está logueado, redirigimos al login
    exit();
}

// Conexión a la base de datos
$servidor = "localhost";
$usuarioBD = "u288355303_Keneth";
$claveBD = "1420Genio.";
$baseDeDatos = "u288355303_Usuarios";

$enlace = mysqli_connect($servidor, $usuarioBD, $claveBD, $baseDeDatos);
if (mysqli_connect_errno()) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener los datos del usuario
$usuario_id = $_SESSION['usuario_id'];

$query = "SELECT u.username, u.correo, p.nombre, p.apellido, p.carrera, p.semestre, p.foto_perfil, p.informacion_extra 
          FROM usuarios u
          LEFT JOIN perfiles p ON u.id = p.usuario_id
          WHERE u.id = ?";

if ($stmt = mysqli_prepare($enlace, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($fila = mysqli_fetch_assoc($resultado)) {
        // Si el usuario tiene un perfil, lo obtenemos
        $usuario = $fila['username'];
        $correo = $fila['correo'];
        $nombre = $fila['nombre'];
        $apellido = $fila['apellido'];
        $carrera = $fila['carrera'];
        $semestre = $fila['semestre'];
        $foto_perfil = $fila['foto_perfil'];
        $informacion_extra = $fila['informacion_extra'];
    }
    mysqli_stmt_close($stmt);
} else {
    // Error en la consulta
    echo "Error al obtener los datos del usuario.";
}

// Cerrar conexión
mysqli_close($enlace);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo $usuario; ?></title>
    <link rel="stylesheet" href="css/misestilos.css">
</head>

<body>
    <div class="form-container">
        <h1>Mi Perfil</h1>
        
        <div class="perfil-info">
            <!-- Foto de perfil -->
            <img src="uploads/<?php echo $foto_perfil; ?>" alt="Foto de perfil" class="foto-perfil">

            <div class="perfil-details">
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?> <?php echo htmlspecialchars($apellido); ?></p>
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($carrera); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($semestre); ?></p>
                <p><strong>Información adicional:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($informacion_extra)); ?></p>
            </div>

            <!-- Enlace para editar el perfil -->
            <div class="edit-link">
                <a href="editar_perfil.php">Editar perfil</a>
            </div>
        </div>

        <div class="logout-link">
            <a href="php/logout.php">Cerrar sesión</a>
        </div>
    </div>
</body>

</html>
