<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página de Feed Social</title>
  <style>
    /* Estilos generales */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f0f2f5;
    }

    /* Estilos del encabezado */
    header {
      background-color: #3b5998;
      color: #fff;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
    }

    .search-bar {
      width: 300px;
      padding: 8px 12px;
      border-radius: 20px;
      border: none;
      outline: none;
    }

    /* Estilos de las publicaciones */
    .post-container {
      margin: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .post-header {
      display: flex;
      align-items: center;
      padding: 12px;
    }

    .profile-picture {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 12px;
    }

    .username {
      font-weight: bold;
    }

    .post-content {
      padding: 12px;
    }

    .post-image {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 8px;
    }

    .post-actions {
      display: flex;
      justify-content: space-between;
      padding: 12px;
    }

    .like-button,
    .comment-button,
    .share-button {
      background-color: transparent;
      border: none;
      color: #606770;
      cursor: pointer;
      display: flex;
      align-items: center;
      font-size: 14px;
    }

    .like-button:hover,
    .comment-button:hover,
    .share-button:hover {
      color: #1877f2;
    }
  </style>
</head>

<body>
  <header>
    <div class="logo">Página de Feed Social</div>
    <input type="text" class="search-bar" placeholder="Buscar">
  </header>

  <?php
    // Array de publicaciones de ejemplo
    $posts = array(
      array(
        'profile_picture' => '/profile-picture-1.jpg',
        'username' => 'Usuario 1',
        'post_image' => '/post-image-1.jpg',
        'post_content' => 'Este es el contenido de la primera publicación.'
      ),
      array(
        'profile_picture' => '/profile-picture-2.jpg',
        'username' => 'Usuario 2',
        'post_content' => 'Este es el contenido de la segunda publicación.'
      )
    );

    // Recorrer el array de publicaciones y mostrarlas
    foreach ($posts as $post) {
  ?>
  <div class="post-container">
    <div class="post-header">
      <img src="<?php echo $post['profile_picture']; ?>" alt="Perfil" class="profile-picture">
      <div class="username"><?php echo $post['username']; ?></div>
    </div>
    <div class="post-content">
      <?php if (isset($post['post_image'])) { ?>
        <img src="<?php echo $post['post_image']; ?>" alt="Publicación" class="post-image">
      <?php } ?>
      <p><?php echo $post['post_content']; ?></p>
    </div>
    <div class="post-actions">
      <button class="like-button">
        <i class="fas fa-thumbs-up"></i> Me gusta
      </button>
      <button class="comment-button">
        <i class="fas fa-comment"></i> Comentar
      </button>
      <button class="share-button">
        <i class="fas fa-share"></i> Compartir
      </button>
    </div>
  </div>
  <?php } ?>
</body>
</html>