Sistema de Gestión Vehicular y Deudas
Requisitos Previos:

Servidor local (XAMPP, MAMP, Laragon, etc.) con PHP 7.4+ y MySQL.

Instrucciones de Instalación:

Importar el archivo transito.sql en MySQL para crear la base de datos control_transito.

Las credenciales por defecto en backend/db.php son root sin contraseña. Modificar si es necesario según el entorno local.

Mover la carpeta del proyecto a la carpeta pública del servidor (ej. htdocs en XAMPP).

Acceder en el navegador a la ruta exacta: http://localhost/reto-inevada/frontend/index.html.

Seguridad:
Toda la interacción con la base de datos utiliza PDO con sentencias preparadas nativas de PHP, previniendo cualquier tipo de ataque de Inyección SQL. También se implementan medidas de seguridad adicional para prevenir XSS.
