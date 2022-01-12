<?php 

require_once __DIR__ . '/../includes/app.php'; //INCLUYE BD, FUNCIONES Y EL AUTOLOAD.

use Controllers\AdminController;
use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\ServicioController;
use MVC\Router;

$router = new Router(); //Todas las rutas tienen un controlador y un método asociado.

//Iniciar Sesion
$router->get('/', [LoginController::class, 'login']); //Cuando visitamos el sitio por primera vez
$router->post('/', [LoginController::class, 'login']); //Iniciar sesión
$router->get('/logout', [LoginController::class, 'logout']); //Cerrar sesión

//Recuperar Password
$router->get('/olvide', [LoginController::class, 'olvide']); //Pag para recuperar contraseña
$router->post('/olvide', [LoginController::class, 'olvide']); //Envia su correo electronico
$router->get('/recuperar', [LoginController::class, 'recuperar']); //Pagina para agregar una nueva contraseña despues de acceder por el enlace enviado a su correo.
$router->post('/recuperar', [LoginController::class, 'recuperar']); //Reescribimos la contraseña

//Crear cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

// Confirmar cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']); //Notificar que se enviaron las instrucciones de confirmar cuenta al email
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']); //Confirmar Cuenta

// Area Privada
$router->get('/cita', [CitaController::class, 'index']); //vista cliente
$router->get('/admin', [AdminController::class, 'index']); //vista Admin

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']); //Ruta con el metodo que consulta los servicios en la BD
$router->post('/api/citas', [APIController::class, 'guardar']); //Ruta con el metodo que lee los datos que enviamos con FormData en JS y almacena la cita
$router->post('/api/eliminar', [APIController::class, 'eliminar']); //Eliminar citas (disponible para el Admin)

//CRUD de Servicios
$router->get('/servicios', [ServicioController::class, 'index']);
$router->get('/servicios/crear', [ServicioController::class, 'crear']);
$router->post('/servicios/crear', [ServicioController::class, 'crear']);
$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();