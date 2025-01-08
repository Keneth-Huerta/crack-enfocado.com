/**
 * Editar perfil de usuario.
 *
 * Este script permite a los usuarios
 <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../php/conexion.php'; // Conexión a la base de datos

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo "Por favor, inicie sesión para continuar.";
    exit();
}

// Obtener el usuario_id de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Obtener la información del usuario
$query = "SELECT * FROM perfiles WHERE usuario_id = ?";
$stmt = mysqli_prepare($enlace, $query);
mysqli_stmt_bind_param($stmt, "i", $usuario_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$perfil = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

// Verificar si el perfil existe
if ($perfil === null) {
    // Si no existe el perfil, inicializamos los valores en blanco
    $perfil = [
        'usuario_id' => $usuario_id, // Agregamos el usuario_id
        'nombre' => '',
        'apellido' => '',
        'carrera' => '',
        'semestre' => '',
        'foto_perfil' => '',
        'foto_portada' => '',
        'informacion_extra' => '',
        'telefono' => ''
    ];
}

// Procesar los cambios del formulario
// Corrección en la verificación del perfil existente
if ($perfil === null) {
    // Si no existe el perfil, inicializamos los valores en blanco
    $perfil = [
        'usuario_id' => $usuario_id, // Agregamos el usuario_id
        'nombre' => '',
        'apellido' => '',
        'carrera' => '',
        'semestre' => '',
        'foto_perfil' => '',
        'foto_portada' => '',
        'informacion_extra' => '',
        'telefono' => ''
    ];
}

// En la parte del procesamiento del formulario POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $carrera = $_POST['carrera'];
    $semestre = $_POST['semestre'];
    $informacion_extra = $_POST['informacion_extra'];
    $telefono = $_POST['telefono'];

    // Manejo de las fotos
    $foto_perfil = $perfil['foto_perfil']; // Valor por defecto
    $foto_portada = $perfil['foto_portada']; // Valor por defecto

    // Procesar foto de perfil si se subió una nueva
    if (!empty($_FILES['foto_perfil']['name'])) {
        $foto_perfil = '../media/uploads/' . basename($_FILES['foto_perfil']['name']);
        move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_perfil);
    }

    // Procesar foto de portada si se subió una nueva
    if (!empty($_FILES['foto_portada']['name'])) {
        $foto_portada = '../media/uploads/' . basename($_FILES['foto_portada']['name']);
        move_uploaded_file($_FILES['foto_portada']['tmp_name'], $foto_portada);
    }

    try {
        // Verificar si el perfil existe
        $check_query = "SELECT usuario_id FROM perfiles WHERE usuario_id = ?";
        $check_stmt = mysqli_prepare($enlace, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $usuario_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $exists = mysqli_fetch_assoc($check_result);
        mysqli_stmt_close($check_stmt);

        if ($exists) {
            // Actualizar perfil existente
            $update_query = "UPDATE perfiles SET 
                           nombre = ?, 
                           apellido = ?, 
                           carrera = ?, 
                           semestre = ?, 
                           foto_perfil = ?, 
                           foto_portada = ?, 
                           informacion_extra = ?,
                           telefono = ? 
                           WHERE usuario_id = ?";

            $stmt = mysqli_prepare($enlace, $update_query);
            mysqli_stmt_bind_param(
                $stmt,
                "sssissssi",
                $nombre,
                $apellido,
                $carrera,
                $semestre,
                $foto_perfil,
                $foto_portada,
                $informacion_extra,
                $telefono,
                $usuario_id
            );
        } else {
            // Insertar nuevo perfil
            $insert_query = "INSERT INTO perfiles 
                           (usuario_id, nombre, apellido, carrera, semestre, 
                            foto_perfil, foto_portada, informacion_extra, telefono) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($enlace, $insert_query);
            mysqli_stmt_bind_param(
                $stmt,
                "ississsss",
                $usuario_id,
                $nombre,
                $apellido,
                $carrera,
                $semestre,
                $foto_perfil,
                $foto_portada,
                $informacion_extra,
                $telefono
            );
        }

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al guardar los cambios: " . mysqli_error($enlace));
        }
        mysqli_stmt_close($stmt);

        // Redirigir al perfil después de guardar cambios
        header("Location: perfil.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
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
    <title>Editar perfil</title>
    <link rel="stylesheet" href="../css/editar_perfil.css">
</head>

<body>
    <div class="form-container">
        <h1>Editar perfil</h1>
        <form method="POST" action="editar_perfil.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($perfil['nombre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($perfil['apellido'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <select id="carrera" name="carrera" required>
                    <option value="Técnico en Aeronáutica" <?php echo ($perfil['carrera'] == 'Técnico en Aeronáutica') ? 'selected' : ''; ?>>Técnico en Aeronáutica</option>
                    <option value="Técnico en Computación" <?php echo ($perfil['carrera'] == 'Técnico en Computación') ? 'selected' : ''; ?>>Técnico en Computación</option>
                    <option value="Técnico en Manufactura Asistida por Computadora" <?php echo ($perfil['carrera'] == 'Técnico en Manufactura Asistida por Computadora') ? 'selected' : ''; ?>>Técnico en Manufactura Asistida por Computadora</option>
                    <option value="Técnico en Sistemas Automotrices" <?php echo ($perfil['carrera'] == 'Técnico en Sistemas Automotrices') ? 'selected' : ''; ?>>Técnico en Sistemas Automotrices</option>
                    <option value="Técnico en Sistemas Digitales" <?php echo ($perfil['carrera'] == 'Técnico en Sistemas Digitales') ? 'selected' : ''; ?>>Técnico en Sistemas Digitales</option>
                </select>
            </div>

            <div class="form-group">
                <label for="semestre">Semestre:</label>
                <input type="number" id="semestre" name="semestre" value="<?php echo htmlspecialchars($perfil['semestre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="foto_perfil">Foto de perfil:</label>
                <input type="file" id="foto_perfil" name="foto_perfil">
            </div>

            <div class="form-group">
                <label for="foto_portada">Foto de portada:</label>
                <input type="file" id="foto_portada" name="foto_portada">
            </div>

            <div class="form-group">
                <label for="informacion_extra">Información extra:</label>
                <textarea id="informacion_extra" name="informacion_extra"><?php echo htmlspecialchars($perfil['informacion_extra'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="telefono">WhatsApp (incluye código de país):</label>
                <input type="tel" id="telefono" name="telefono"
                    pattern="[0-9]+"
                    placeholder="Ejemplo: 525512345678"
                    value="<?php echo htmlspecialchars($perfil['telefono'] ?? ''); ?>"
                    required>
                <small>Formato: código de país + número (sin espacios ni símbolos)</small>
            </div>
            <button type="submit">Guardar cambios</button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para mostrar vista previa de imagen
            function previewImage(input, previewId) {
                const preview = document.getElementById(previewId);
                const file = input.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (!preview.querySelector('img')) {
                            const img = document.createElement('img');
                            preview.innerHTML = '';
                            preview.appendChild(img);
                        }
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('empty');
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '<span>Vista previa no disponible</span>';
                    preview.classList.add('empty');
                }
            }

            // Agregar previsualizaciones después de los inputs de archivo
            const fotoPerfil = document.getElementById('foto_perfil');
            const fotoPortada = document.getElementById('foto_portada');

            // Crear contenedores de vista previa
            const previewPerfil = document.createElement('div');
            previewPerfil.id = 'preview_perfil';
            previewPerfil.className = 'image-preview empty';
            previewPerfil.innerHTML = '<span>Vista previa no disponible</span>';
            fotoPerfil.parentNode.appendChild(previewPerfil);

            const previewPortada = document.createElement('div');
            previewPortada.id = 'preview_portada';
            previewPortada.className = 'image-preview empty';
            previewPortada.innerHTML = '<span>Vista previa no disponible</span>';
            fotoPortada.parentNode.appendChild(previewPortada);

            // Agregar eventos para actualizar las vistas previas
            fotoPerfil.addEventListener('change', function() {
                previewImage(this, 'preview_perfil');
            });

            fotoPortada.addEventListener('change', function() {
                previewImage(this, 'preview_portada');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>