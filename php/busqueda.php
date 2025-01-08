/**
 * SearchSystem class handles the search functionality for users, posts, and products.
 * 
 * @property mysqli $enlace The database connection link.
 * @property string $searchTerm The search term used for querying the database.
 * @property array $results The results of the search queries.
 * 
 * @method __construct(mysqli $enlace, string $searchTerm) Initializes the search system with a database connection and search term.
 * @method void searchUsers() Executes a search query for users based on the search term.
 * @method void searchPosts() Executes a search query for posts based on the search term.
 * @method void searchProducts() Executes a search query for products based on the search term.
 * @method array getResults() Returns the results of the search queries.
 * 
 * Usage:
 * $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
 * $search = new SearchSystem($enlace, $searchTerm);
 * if ($searchTerm != '') {
 *     $search->searchUsers();
 *     $search->searchPosts();
 *     $search->searchProducts();
 * }
 * $resultados = $search->getResults();
 */
<?php
require_once 'conexion.php';

class SearchSystem
{
    private $enlace;
    private $searchTerm;
    private $results;

    public function __construct($enlace, $searchTerm)
    {
        $this->enlace = $enlace;
        $this->searchTerm = '%' . trim($searchTerm) . '%';
        $this->results = [
            'usuarios' => null,
            'publicaciones' => null,
            'productos' => null
        ];
    }

    public function searchUsers()
    {
        $query = "
            SELECT 
                p.usuario_id,
                p.nombre,
                p.apellido,
                p.carrera,
                p.foto_perfil,
                u.username,
                u.correo,
                (SELECT COUNT(*) FROM productos prod WHERE prod.usuario_id = p.usuario_id) as total_productos
            FROM perfiles p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE 
                CONCAT(p.nombre, ' ', p.apellido) LIKE ? 
                OR p.carrera LIKE ? 
                OR u.username LIKE ? 
                OR u.correo LIKE ?
            ORDER BY p.nombre ASC
        ";

        $stmt = mysqli_prepare($this->enlace, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'ssss',
            $this->searchTerm,
            $this->searchTerm,
            $this->searchTerm,
            $this->searchTerm
        );
        mysqli_stmt_execute($stmt);
        $this->results['usuarios'] = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    }

    public function searchPosts()
    {
        $query = "
            SELECT 
                p.*,
                u.username,
                pf.nombre,
                pf.apellido,
                pf.foto_perfil
            FROM publicaciones p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN perfiles pf ON u.id = pf.usuario_id
            WHERE p.contenido LIKE ?
            ORDER BY p.fecha_publicada DESC
        ";

        $stmt = mysqli_prepare($this->enlace, $query);
        mysqli_stmt_bind_param($stmt, 's', $this->searchTerm);
        mysqli_stmt_execute($stmt);
        $this->results['publicaciones'] = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    }

    public function searchProducts()
    {
        $query = "
            SELECT 
                p.*,
                u.username,
                pf.nombre as vendedor_nombre,
                pf.apellido as vendedor_apellido,
                pf.foto_perfil
            FROM productos p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN perfiles pf ON u.id = pf.usuario_id
            WHERE 
                p.producto LIKE ? 
                OR p.descripcion LIKE ?
            ORDER BY p.idProducto DESC
        ";

        $stmt = mysqli_prepare($this->enlace, $query);
        mysqli_stmt_bind_param(
            $stmt,
            'ss',
            $this->searchTerm,
            $this->searchTerm
        );
        mysqli_stmt_execute($stmt);
        $this->results['productos'] = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    }

    public function getResults()
    {
        return $this->results;
    }
}

// Inicializar búsqueda
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$search = new SearchSystem($enlace, $searchTerm);

if ($searchTerm != '') {
    $search->searchUsers();
    $search->searchPosts();
    $search->searchProducts();
}

$resultados = $search->getResults();
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
    <title>Resultados de búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/estilosprin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .search-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .producto-imagen {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .search-highlight {
            background-color: yellow;
            padding: 2px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container mt-4">
        <h2>Búsqueda Avanzada</h2>

        <!-- Formulario de búsqueda mejorado -->
        <form method="get" action="busqueda.php" class="mb-4">
            <div class="input-group">
                <input type="text"
                    name="search"
                    class="form-control"
                    placeholder="Buscar usuarios, publicaciones o productos..."
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                    required
                    minlength="3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>

        <!-- Tabs para navegación entre resultados -->
        <ul class="nav nav-tabs search-tabs mb-4" style="color: #000 !important;">
            <li class="nav-item" style="color: #000 !important;">
                <a class="nav-link active" data-bs-toggle="tab" href="#usuarios" style="color: #000 !important;">
                    Usuarios
                    <?php if ($resultados['usuarios']): ?>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($resultados['usuarios']); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" style="color: #000 !important;">
                <a class="nav-link" data-bs-toggle="tab" href="#publicaciones" style="color: #000 !important;">
                    Publicaciones
                    <?php if ($resultados['publicaciones']): ?>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($resultados['publicaciones']); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item" style="color: #000 !important;">
                <a class="nav-link" data-bs-toggle="tab" href="#productos" style="color: #000 !important;">
                    Productos
                    <?php if ($resultados['productos']): ?>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($resultados['productos']); ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>

        <!-- Contenido de los tabs -->
        <div class="tab-content">
            <!-- Tab Usuarios -->
            <div class="tab-pane fade show active" id="usuarios">
                <?php if ($resultados['usuarios'] && mysqli_num_rows($resultados['usuarios']) > 0): ?>
                    <div class="row">
                        <?php while ($usuario = mysqli_fetch_assoc($resultados['usuarios'])): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body d-flex">
                                        <img src="<?php echo htmlspecialchars($usuario['foto_perfil'] ?? '../media/user.png'); ?>"
                                            alt="Perfil"
                                            class="rounded-circle me-3"
                                            style="width: 64px; height: 64px; object-fit: cover;">
                                        <div>
                                            <h5 class="card-title">
                                                <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                                            </h5>
                                            <p class="card-text">
                                                <small class="text-muted">@<?php echo htmlspecialchars($usuario['username']); ?></small><br>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($usuario['carrera']); ?></span>
                                                <span class="badge bg-success"><?php echo $usuario['total_productos']; ?> productos</span>
                                            </p>
                                            <a href="perfil.php?usuario_id=<?php echo $usuario['usuario_id']; ?>"
                                                class="btn btn-primary btn-sm">Ver Perfil</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No se encontraron usuarios.</div>
                <?php endif; ?>
            </div>

            <!-- Tab Publicaciones -->
            <div class="tab-pane fade" id="publicaciones">
                <?php if ($resultados['publicaciones'] && mysqli_num_rows($resultados['publicaciones']) > 0): ?>
                    <?php while ($pub = mysqli_fetch_assoc($resultados['publicaciones'])): ?>
                        <div class="card mb-3">
                            <div class="card-header d-flex align-items-center">
                                <img src="<?php echo htmlspecialchars($pub['foto_perfil'] ?? '../media/user.png'); ?>"
                                    alt="Perfil"
                                    class="rounded-circle me-2"
                                    style="width: 32px; height: 32px; object-fit: cover;">
                                <div>
                                    <strong><?php echo htmlspecialchars($pub['nombre'] . ' ' . $pub['apellido']); ?></strong>
                                    <small class="text-muted">@<?php echo htmlspecialchars($pub['username']); ?></small>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($pub['contenido'])); ?></p>
                                <small class="text-muted">
                                    Publicado el <?php echo date("d/m/Y H:i", strtotime($pub['fecha_publicada'])); ?>
                                </small>
                            </div>
                            <div class="card-footer">
                                <a href="detalle_publicacion.php?id=<?php echo $pub['id_publicacion']; ?>"
                                    class="btn btn-info btn-sm">Ver Detalles</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">No se encontraron publicaciones.</div>
                <?php endif; ?>
            </div>

            <!-- Tab Productos -->
            <div class="tab-pane fade" id="productos">
                <?php if ($resultados['productos'] && mysqli_num_rows($resultados['productos']) > 0): ?>
                    <div class="row">
                        <?php while ($producto = mysqli_fetch_assoc($resultados['productos'])): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <?php
                                            $imagen_base64 = '';
                                            if ($producto['imagen']) {
                                                $imagen_base64 = base64_encode($producto['imagen']);
                                            }
                                            ?>
                                            <img src="<?php echo $imagen_base64 ? 'data:image/jpeg;base64,' . $imagen_base64 : '../media/no-image.png'; ?>"
                                                alt="Producto"
                                                class="producto-imagen me-3">
                                            <div>
                                                <h5 class="card-title"><?php echo htmlspecialchars($producto['producto']); ?></h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        Vendedor: <?php echo htmlspecialchars($producto['vendedor_nombre'] . ' ' . $producto['vendedor_apellido']); ?>
                                                    </small><br>
                                                    <strong class="text-success">$<?php echo number_format($producto['precio'], 2); ?></strong>
                                                </p>
                                                <p class="card-text">
                                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                                </p>
                                                <a href="detalle_producto.php?id=<?php echo $producto['idProducto']; ?>"
                                                    class="btn btn-primary btn-sm">Ver Detalles</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No se encontraron productos.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>