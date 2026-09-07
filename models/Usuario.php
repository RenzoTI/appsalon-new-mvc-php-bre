<?php


namespace Model;

class Usuario extends ActiveRecord {
    // * Base de Datos

  protected static $tabla = 'usuarios';
  protected static $columnasDB = ['id', 'nombre', 'apellido', 'email',
  'password', 'telefono', 'admin', 'confirmado', 'token'];

  // * Creamos un atributo por cada uno de ellos
  public $id;
  public $nombre;
  public $apellido;
  public $email;
  public $password;
  public $telefono;
  public $admin;
  public $confirmado;
  public $token;
  

  // * Creamos nuestro constructor, no necesita instanciarse
  public function __construct($args = [])
  {
    $this->id = $args['id'] ?? null;
    $this->nombre = $args['nombre'] ?? '';
    $this->apellido = $args['apellido'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->telefono = $args['telefono'] ?? '';
    $this->admin = $args['admin'] ?? '0';
    $this->confirmado = $args['confirmado'] ?? '0';
    $this->token = $args['token'] ?? '';
  }

  // Mensajes de validacion para la creacion de una cuenta

    public function validarNuevaCuenta(){
    if(!$this->nombre){
        self::$alertas['error'][] = 'El Nombre es obligatorio';
    }
    if(!$this->apellido){
        self::$alertas['error'][] = 'El Apellido es obligatorio';
    }
    if(!$this->email){
        self::$alertas['error'][] = 'El Email es obligatorio';
    }
    if(!$this->password){
        self::$alertas['error'][] = 'El Password es obligatorio';
    }
    if(strlen($this->password) < 6){
        self::$alertas['error'][] = 'El Password debe contener almenos 6 caracteres';
    }
    
    return self::$alertas;
    }

    public function validarLogin(){
    if(!$this->email){
      self::$alertas['error'][] = 'El Email es obligatorio';
    }
    if(!$this->password){
      self::$alertas['error'][] = 'El Password es obligatorio';
    }
    return self::$alertas;
  }

    public function validarEmail()
    {
      if(!$this->email){
        self::$alertas['error'][] = 'El Email es obligatorio';
      }    
      return self::$alertas;    
    }

    public function validarPassword()
    {
      if(!$this->password){
        self::$alertas['error'][] = 'El Password es obligatorio';
      }
      if(strlen($this->password) < 6){
        self::$alertas['error'][] = 'El Password debe tener almenos 6 caracteres';
      }     
      return self::$alertas;    
    }
      // Revisa si el usuario ya existe
  public function existeUsuario() {
    $query= " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

    $resultado = self::$db->query($query);

    //debuguear($resultado);

        if($resultado->num_rows){
        self::$alertas['error'][] = 'El Usuario ya esta registrado';
        }

    return $resultado;
  }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); // * Toma el mismo password y lo encripta.
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordandVerificado($password){
      //1ero pass que nos dio el usuario y 2do el de la base de datos
      $resultado = password_verify($password, $this->password); // ? NO brinda un bolean
      //debuguear($this);
      if(!$resultado || !$this->confirmado){
        self::$alertas['error'][] = 'El Password Incorrecto o tu cuenta no ha sido confirmada';
        
      }else {
        return true;
      }
    }
}