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
        !isset($_SESSION['usuario']) ? Redirect::to('/usuario/ingresar') : null;
        //Se buscan los datos del usuario para mostrarlos en el header
        $data['usuario'] = $_SESSION['usuario'];

        $preguntaYRespuestas = $this->model->obtenerPreguntaAlAzar();

        if ($preguntaYRespuestas) {
            //Esto define el color de la categoría en la vista
            $categoriaEstilos = [
                'Ciencia' => 'success',
                'Historia' => 'warning',
                'Entretenimiento' => 'info',
                'Geografía' => 'primary',
                'Arte' => 'danger'
            ];

            $categoria = $preguntaYRespuestas['categoria'];
            $estiloCategoria = $categoriaEstilos[$categoria] ?? 'bg-light';

            $data = [
                'usuario' => $_SESSION['usuario'],
                'pregunta' => $preguntaYRespuestas['pregunta'],
                'respuestas' => $preguntaYRespuestas['respuestas'],
                'categoria' => $preguntaYRespuestas['categoria'],
                'categoriaEstilo' => $estiloCategoria,
            ];

            $this->render->printView('juego', $data);
        } else {
            Logger::info($_SESSION["errorRespuesta"] = "Ha ocurrido un error al cargar las respuestas");
        }
    }
}