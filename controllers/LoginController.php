<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router)
    {
        //echo " Desde Login";

        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === 'POST'){
            //echo "Desde Post";

            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();
            //debuguear($alertas);

            if(empty($alertas)){
                //Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                //debuguear($usuario);

                if($usuario){
                    // * Verificar el Password
                    //debuguear($usuario);
                    if($usuario->comprobarPasswordandVerificado($auth->password)){

                    //Autenticar el usuario
                        if(!isset($_SESSION)) {
                            session_start();
                        };

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //debuguear($_SESSION);

                        // Redireccionamiento

                       // debuguear($usuario->admin); //? Nos vota 0 por que no somos admin

                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }

                        
                    }
                    
                }else{
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
       // echo "Desde logout..";
       session_start();
       // debuguear($_SESSION);

       $_SESSION = [];

       header('Location: /');
    }

    public static function olvide(Router $router)
    {
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === 'POST'){

            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            //debuguear($auth);

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                    if($usuario && $usuario->confirmado === "1"){

                        //Generar un token
                        $usuario->crearToken();
                        $usuario->guardar();

                        // Enviar Email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarInstruciones();


                        //Alerta de exito
                        Usuario::setAlerta('exito', 'Revisa tu Email');

                        //debuguear($usuario);

                        
                    }else {
                        Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                        
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
        //echo " Desde recuperar";
        $alertas = [];
        $error =false;


        $token = s($_GET['token']);

        //Buscar Usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Valido');
            $error=true;
        }

       // debuguear($usuario);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            //Leer e nuevo password y leerlo

            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){

                $usuario->password = '';

                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = '';

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
                
            }
            //debuguear($password);
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password',[
            'alertas' => $alertas,
            'error' => $error
            

        ]);
    }

    public static function crear(Router $router)
    {   
        // ? Para que el formulario llene automaticamente los datos que esten bien creamos una instancia sin parametros
        $usuario = new Usuario;
        // * Consultamos que tiene la varible $usuario con el debuguear.
        //debuguear($usuario); // * Nos arroja un objeto con arreglos vacios
        
        // Alertas vacias
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === 'POST'){
           //  echo "Enviaste el formulario";
           // $usuario = new Usuario($_POST); // * Le pasamos todos lo datos que tengamos en POST
            // * Consultamos que tiene la varible $usuario con el debuguear.
           // debuguear($usuario); // * Nos arroja un objeto con arreglos vacios

            // ? Y uno de los métodos de Active Record si recuerdas es el de sincronizar, sincronizar va a ir iterando
            // ? en cada uno de esos datos que estamos enviando por post y va a sincronizar el objeto que está vacío
            // ? con los datos nuevos que han llegado por POST.
           $usuario->sincronizar($_POST);

           // ? Para llamar a ese método validarNuevaCuenta ponemos: usuario y colocamos la sintaxis de flecha para acceder a ese atributo.
           // ? $usuario->validarNuevaCuenta(); 
           // * Ahora este método nos va a retornar las alertas, por lo tanto aquí le puedo poner alertas.
            $alertas = $usuario->validarNuevaCuenta();
            //debuguear($alertas);


            // * Revisar que alerta este vacio
            if(empty($alertas)){
               // echo "Pasaste la validacion";
               
               // * Verificar que el usaurio no este registrado
              $resultado =  $usuario->existeUsuario(); // Como arroja un resultado le asignamos una variable resultado

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                } else{
                    //No esta registrado
                    //debuguear('No esta registrado');

                    // * Hashear el Password
                    $usuario->hashPassword();

                    // * Generar un token Unico
                    $usuario->crearToken();

                    // * Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    // * enviar el email de confirmacion , creamo el metodo en la clase email y lo en el LoginController
                    $email->enviarConfirmacion();

                    // * Crear el usuario
                    $resultado = $usuario->guardar();


                   // debuguear($usuario);

                   if($resultado){
                        //echo "Guardado Correctamente";
                        header('Location: /mensaje');
                   }
                } 
            }
        }

        $router->render('auth/crear-cuenta', [
            // ? En MVC podemos pasar datos , variables a la vista.
                'usuario' => $usuario, // * Le paso la variable $usuario y el obejeto con arreglos vacios ya va estar disponible en la vista
                'alertas' => $alertas // *  Le paso la variable $alertas  hacia la vista
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET['token']);
        // debuguear($token);
        $usuario = Usuario::where('token', $token);
        // debuguear($usuario);

            if(empty($usuario)){
                // * Mostrar Mensaje de Errror
                // echo "Token no valido";
                Usuario::setAlerta('error', 'Token No Válido'); // * Seteo la alerta con el metodo del ActiveRecord, y se queda en memoria
            }else {
                // * Modificar a usuario confirmado
               //echo "Token valido, confirmando usuario..";

               $usuario->confirmado = "1";
               $usuario->token = "";
               $usuario->guardar();
               Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
               //debuguear($usuario);
            }
            // * Obtener alertas
            $alertas = Usuario::getAlertas(); // * Aca lee la alerta crwada en memoria y pueda ser renderisada a la vista

            // ? Renderizar la vista
            $router->render('auth/confirmar-cuenta', [
                    'alertas' => $alertas
            ]);
    }
}