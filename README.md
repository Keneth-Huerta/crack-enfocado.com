# Proyecto de Red Social

Este proyecto es una aplicación de red social simple que permite a los usuarios agregar comentarios a publicaciones. A continuación se describen los archivos y su funcionalidad:

## Estructura del Proyecto

- **src/agregar_comentario.php**: Maneja la lógica para agregar comentarios a las publicaciones. Verifica si el usuario está logueado, obtiene los datos del formulario y los inserta en la base de datos.

- **src/publicaciones.php**: Se encarga de mostrar las publicaciones y los comentarios asociados. Incluye la lógica para recuperar datos de la base de datos y presentarlos en la interfaz.

- **src/conexion.php**: Establece la conexión a la base de datos. Contiene la configuración necesaria para conectarse a la base de datos MySQL.

- **src/index.php**: Página de inicio de la aplicación. Redirige a los usuarios a otras secciones de la aplicación o muestra contenido relevante.

- **css/styles.css**: Contiene los estilos CSS para la aplicación, definiendo la apariencia visual de las páginas.

- **js/main.js**: Incluye la lógica de JavaScript para la interacción del lado del cliente, como la validación de formularios o la manipulación del DOM.

- **config/database.php**: Contiene la configuración de la base de datos, como el nombre de usuario, la contraseña y el nombre de la base de datos.

## Instalación

1. Clona el repositorio.
2. Configura la base de datos en `config/database.php`.
3. Abre `src/index.php` en tu navegador.

## Contribuciones

Las contribuciones son bienvenidas. Si deseas colaborar, por favor abre un issue o envía un pull request.