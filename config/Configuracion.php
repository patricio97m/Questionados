<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once('helper/Database.php');
include_once('helper/Render.php');
include_once('helper/MustacheRender.php');
include_once("helper/Router.php");
include_once("helper/Logger.php");
include_once('helper/Redirect.php');

include_once('controller/HomeController.php');
include_once('model/HomeModel.php');
include_once('controller/UsuarioController.php');
include_once("model/UsuarioModel.php");
include_once('controller/JuegoController.php');
include_once("model/JuegoModel.php");


include_once('third-party/mustache/src/Mustache/Autoloader.php');
include_once('third-party/PHPMailer/src/Exception.php');
include_once('third-party/PHPMailer/src/PHPMailer.php');
include_once('third-party/PHPMailer/src/SMTP.php');

class Configuracion {
    public function __construct() {
    }

    public function getDatabase() {
        $config = parse_ini_file('configuration.ini');
        $database = new Database(
            $config['servername'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );
        return $database;
    }

    public function getRender() {
        //return new Render("view/header.php", "view/footer.php");
        return new MustacheRender();
    }


    public function getUsuarioController() {
        $model = new UsuarioModel($this->getDatabase());
        return new UsuarioController($this->getRender(), $model, $this->getMailer());
    }

    public function getJuegoController() {
        $model = new JuegoModel($this->getDatabase());
        return new JuegoController($this->getRender(), $model);
    }

    public function getHomeController() {
        $model = new HomeModel($this->getDatabase());
        return new HomeController($this->getRender(), $model);
    }

    public function getRouter() {
        return new Router($this,"getHomeController","index");
    }

    public function getMailer(){
        $config = parse_ini_file('configuration.ini');
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $config['mailerhost'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $config['mailerusername'];                     //SMTP username
        $mail->Password   = $config['mailerpassword'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = $config['mailerport'];

        return $mail;
    }
}
