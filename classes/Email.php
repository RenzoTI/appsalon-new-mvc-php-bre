<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;


class Email {

    public $email;
    public $nombre;
    public $token;


    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){

        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        //Recipients
        $mail->setFrom('produ@appsalon.com');
        //$mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->addAddress($this->email);
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML
        $mail->isHTML(true);
        $mail->Charset = 'UTF-8'; 

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre .  "</p> Has creado tu cuenta en AppSalon, solo debes confirmarla el siguiente enlace</strong>";
        $contenido .= "<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a>";
        $contenido .= "<p>Si no solicitastes esta cuenta, puedes ignorar le mensaje </p>";
        $contenido .= '</html>';
        $mail->Body = $contenido;

        //Enviar el mail
        $mail->send();


    }

    public function enviarInstruciones(){

         //Crear el objeto de email
         $mail = new PHPMailer();
         $mail->isSMTP();
         $mail->Host = $_ENV['EMAIL_HOST'];
         $mail->SMTPAuth = true;
         $mail->Port = $_ENV['EMAIL_PORT'];
         $mail->Username = $_ENV['EMAIL_USER'];
         $mail->Password = $_ENV['EMAIL_PASS'];
 
         //Recipients
         $mail->setFrom('produ@appsalon.com');
         //$mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
         $mail->addAddress($this->email);
         $mail->Subject = 'Restablece tu Pasword';
 
         // Set HTML
         $mail->isHTML(true);
         //$mail->Charset = 'UTF-8'; 
 
         $contenido = '<html>';
         $contenido .= "<p><strong>Hola " . $this->nombre .  "</strong> Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
         $contenido .= "<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "'>Restablecer Password</a>";
         $contenido .= "<p>Si no solicitastes esta cuenta, puedes ignorar le mensaje </p>";
         $contenido .= '</html>';
         $mail->Body = $contenido;
 
         //Enviar el mail
         $mail->send();
 

    }

}