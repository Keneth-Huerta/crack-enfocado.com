<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="../css/misestilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tabs-container {
            display: flex;
            flex-direction: column;
            margin: 20px 0;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            background-color: #f0f0f0;
            border-bottom: 2px solid #ddd;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            color: #555;
        }

        .tab.active {
            color: #000;
            border-bottom: 2px solid #007bff;
        }

        .tab-content {
            display: none;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="perfil-container">
        <!-- Foto de portada -->
        <div class="foto-portada">
            <img src="<?php echo htmlspecialchars($foto_portada); ?>" alt="Foto de portada">
        </div>

        <h1 class="titulo-perfil"><?php echo htmlspecialchars($username); ?></h1>

        <div class="perfil-info">
            <div class="foto-perfil">
                <img src="<?php echo htmlspecialchars($foto_perfils); ?>" alt="Foto de perfil">
            </div>
            <div class="informacion">
                <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></p>
                <p><strong>Carrera:</strong> <?php echo htmlspecialchars($carrera); ?></p>
                <p><strong>Semestre:</strong> <?php echo htmlspecialchars($semestre); ?></p>
                <p><strong>Información Extra:</strong> <?php echo nl2br(htmlspecialchars($informacion_extra)); ?></p>
            </div>
        </div>

        <!-- Sistema de pestañas -->
        <div class="tabs-container">
            <div class="tabs">
                <div class="tab active" data-tab="publicaciones">Publicaciones</div>
                <div class="tab" data-tab="productos">Productos</div>
            </div>

            <div class="tab-content active" id="publicaciones">
                <h2>Publicaciones</h2>
                <?php if ($publicaciones_result && mysqli_num_rows($publicaciones_result) > 0): ?>
                    <div class="lista-publicaciones">
                        <?php while ($publicacion = mysqli_fetch_assoc($publicaciones_result)): ?>
                            <div class="publicacion-item">
                                <div class="publicacion-meta">
                                    <p class="fecha"><i class="far fa-clock"></i>
                                        <?php
                                        $fecha = new DateTime($publicacion['fecha_publicada']);
                                        echo $fecha->format('d/m/Y H:i');
                                        ?>
                                    </p>
                                </div>
                                <div class="publicacion-contenido">
                                    <p><?php echo nl2br(htmlspecialchars($publicacion['contenido'])); ?></p>
                                    <?php if (!empty($publicacion['imagen'])): ?>
                                        <div class="publicacion-imagen">
                                            <img src="<?php echo htmlspecialchars($publicacion['imagen']); ?>" alt="Imagen de publicación">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No hay publicaciones disponibles.</p>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="productos">
                <h2>Productos</h2>
                <?php
                $productos_query = "SELECT * FROM productos WHERE usuario_id = ?";
                if ($stmt_productos = mysqli_prepare($enlace, $productos_query)) {
                    mysqli_stmt_bind_param($stmt_productos, "i", $usuario_ids);
                    mysqli_stmt_execute($stmt_productos);
                    $productos_result = mysqli_stmt_get_result($stmt_productos);

                    if ($productos_result && mysqli_num_rows($productos_result) > 0): ?>
                        <div class="lista-productos">
                            <?php while ($producto = mysqli_fetch_assoc($productos_result)): ?>
                                <div class="producto-item">
                                    <h3><?php echo htmlspecialchars($producto['producto']); ?></h3>
                                    <p><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></p>
                                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                    <?php if (!empty($producto['imagen'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" alt="Imagen de producto">
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No hay productos disponibles.</p>
                <?php endif;
                } ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Cambiar pestaña activa
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    // Mostrar contenido correspondiente
                    const target = tab.dataset.tab;
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                        if (content.id === target) {
                            content.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>