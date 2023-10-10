<?php

class JuegoController
{
    private $render;
    private $model;

    public function __construct($render, $model) {
        $this->render = $render;
        $this->model = $model;
    }

    public function nuevaPartida(){
        $this->render->printView('juego');
    }

}