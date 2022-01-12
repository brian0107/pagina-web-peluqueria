<?php
//Instalar phpmailer: composer require phpmailer/phpmailer //Despues de instalar la dependencia debemos actualizar composer para que autoload cargue la libreria: composer update
namespace Controllers; //Todo archivo dentro de la carpeta controllers debe llevar este namespace por que asi esta definido en composer.json para importarlos automaticamente.

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router) //Al pasar la variable $router, obtenemos la instancia creada en el index.
    { 
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST); //Contiene unicamente email y password ingresado por el usuario.

            $alertas = $auth->validarLogin(); //Valida unicamente que se ingrese email y password

            if(empty($alertas)) {
                // Comprobar que exista el usuario (por medio del email)
                $usuario = Usuario::where('email',$auth->email); //Consultamos la BD y obtenemos el objeto con los datos del usuario encontrado
                
                if($usuario){ //Si se encontro un usuario

                    // Verificar el password y que el usuario este verificado
                    if( $usuario->comprobarPasswordAndVerificado($auth->password) ){ //Si el password es correcto y esta verificado
                        //Autenticar el usuario
                        if(!isset($_SESSION)) { //Iniciamos una Sesión
                            session_start();
                        }

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if ($usuario->admin ==="1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                        
                    } 
                } else { //Si no se encontro el usuario
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
            
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        if(!isset($_SESSION)) { //Iniciamos la Sesión con los datos del usuario
            session_start();
        }

        $_SESSION = []; //Limpiamos la Sesión

        header('Location: /'); //Redirigimos al Login
    }

    public static function olvide(Router $router)
    {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === "1") {

                    // Generar Token
                    $usuario->crearToken();
                    $usuario->guardar(); // Modificamos el usuario con el nuevo token creado
                    
                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Alerta de Exito
                    Usuario::setAlerta('exito', 'Revisa tu Email');
               
                } else {
                    Usuario::setAlerta('error', 'El Usuario no Existe o no esta Confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Válido');
            $error = true; //Detiene la ejecución del código para que no se muestre el formulario
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo

            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();
                
                if($resultado){
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario; //Objeto para auto completar campos  
        $alertas = []; //Alertas vacias
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST); //Actualizamos el objeto en memoria vacio con los datos enviados en el Post. Ademas con esto el autocompletado no se perdera al enviar el form ya que es el mismo objeto. Así evitamos crear otra instancia de Usuario.
            $alertas = $usuario->validarNuevaCuenta(); //Obtenemos el arreglo de alertas.
            //Revisar que alerta este vacio
            if (empty($alertas)) {
                //Verificar si ya existe un usuario con el email ingresado
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) { //Si ya existe
                    $alertas = Usuario::getAlertas(); //Obtenemos la alerta
                } else {
                    // Hashear el Password
                    $usuario->hashPassword();

                    // Generar un Token único
                    $usuario->crearToken(); //Al crear una cuenta, asiganmos al usuario un token para confirmar la cuenta.

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar(); //Crea el usuario pero sin confirmar la cuenta aún.
                    //debuguear($usuario); 
                    if ($resultado) {
                        header('Location: /mensaje'); //Avisamos al usuario que se envio la confirmacion de cuenta a su email.
                    }
                }
            }
        }
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario, //Estas variables estaran disponibles en el archivo crear-cuenta y en los archivos que se incluyan en el.
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {

        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];
        $token = s($_GET['token']); //Obtenemos el token sanitizado de la URL
        $usuario = Usuario::where('token', $token); // Verificamos que exista un usuario con el token enviado al email.

        if (empty($usuario)) { //Si no hay usuario con ese token 
            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Válido');
        } else { // Si existe usuario entonces modificamos el usuario a confirmado
            $usuario->confirmado = "1";
            $usuario->token = null; //Eliminamos el token 
            //Actualiza el usuario
            $usuario->guardar(); //Actualiza el usuario a confirmado y elimina el token una vez confirmada la cuenta
            //debuguear($usuario); 
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente'); //Mostrar mensaje de exito
        }
        //Obtener alertas
        $alertas = Usuario::getAlertas();
        //Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
