<?php
include_once('helper/Database.php');
include_once('helper/Render.php');
include_once('helper/MustacheRender.php');
include_once("helper/Router.php");
include_once("helper/Logger.php");
include_once('helper/Redirect.php');

include_once('controller/UsuarioController.php');
include_once("model/UsuarioModel.php");
include_once('controller/JuegoController.php');
include_once("model/JuegoModel.php");


include_once('third-party/mustache/src/Mustache/Autoloader.php');

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
        return new UsuarioController($this->getRender(), $model);
    }

    public function getJuegoController() {
        $model = new JuegoModel($this->getDatabase());
        return new JuegoController($this->getRender(), $model);
    }

    public function getRouter() {
        return new Router($this,"getUsuarioController","registro");
    }
}
