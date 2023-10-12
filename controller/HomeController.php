<?php

class HomeController
{
    private $render;
    private $model;

    public function __construct($render, $model) {
        $this->render = $render;
        $this->model = $model;
    }
    public function index() {
        $datosUsuario['usuario'] = $_SESSION['usuario'];
        if ($datosUsuario['usuario']){$this->render->printView('home', $datosUsuario);}
        else Redirect::to('/usuario/ingresar');
    }
}