// script.js
document.getElementById("uploadForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

    const imageInput = document.getElementById("imageInput");
    const file = imageInput.files[0]; // Obtener la imagen cargada

    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imageUrl = e.target.result;
            
            // Guardar la imagen en LocalStorage
            localStorage.setItem("lastImage", imageUrl);
            
            // Crear un nuevo elemento de publicación
            const post = document.createElement("div");
            post.classList.add("post");

            // Estructura de la publicación (puedes personalizarla)
            post.innerHTML = `
                <div class="post-header">
                    <img src="user.jpg" alt="Usuario" class="user-img">
                    <div class="user-info">
                        <span class="username">Usuario</span>
                        <span class="timestamp">Hace un momento</span>
                    </div>
                </div>
                <div class="post-body">
                    <p>¡Mira mi nueva foto!</p>
                    <img src="${imageUrl}" alt="Imagen publicada" class="post-image">
                </div>
                <div class="post-footer">
                    <button class="like-btn">Me gusta</button>
                    <button class="comment-btn">Comentar</button>
                </div>
            `;
            
            // Agregar la nueva publicación al contenedor de publicaciones
            document.getElementById("posts").prepend(post);
            
            // Limpiar el campo de carga de imagen
            imageInput.value = '';
        };
        
        // Leer la imagen como una URL de datos
        reader.readAsDataURL(file);
    }
});

// Al cargar la página, verificar si hay una imagen guardada en LocalStorage
window.addEventListener("load", function() {
    const lastImage = localStorage.getItem("lastImage");

    if (lastImage) {
        // Mostrar la última imagen cargada al recargar la página
        const post = document.createElement("div");
        post.classList.add("post");

        post.innerHTML = `
            <div class="post-header">
                <img src="user.jpg" alt="Usuario" class="user-img">
                <div class="user-info">
                    <span class="username">Usuario</span>
                    <span class="timestamp">Hace un momento</span>
                </div>
            </div>
            <div class="post-body">
                <p>¡Mira mi nueva foto!</p>
                <img src="${lastImage}" alt="Imagen publicada" class="post-image">
            </div>
            <div class="post-footer">
                <button class="like-btn">Me gusta</button>
                <button class="comment-btn">Comentar</button>
            </div>
        `;
        
        // Agregar la publicación a la sección de publicaciones
        document.getElementById("posts").prepend(post);
    }
});
