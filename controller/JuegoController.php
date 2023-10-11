<?php
class JuegoController
{
    private $render;
    private $model;

    public function __construct($render, $model) {
        $this->render = $render;
        $this->model = $model;
    }

    private function cargarPregunta() {
        // Obtener una pregunta al azar
        $preguntaYRespuestas = $this->model->obtenerPreguntaAlAzar();

        if ($preguntaYRespuestas) {
            $categoriaEstilos = [
                'Ciencia' => 'success',
                'Historia' => 'warning',
                'Entretenimiento' => 'info',
                'Geografía' => 'primary',
                'Arte' => 'danger',
                'Deporte' => 'secondary'
            ];

            $categoria = $preguntaYRespuestas['categoria'];
            $estiloCategoria = $categoriaEstilos[$categoria] ?? 'bg-light';

            $data = [
                'usuario' => $_SESSION['usuario'],
                'pregunta' => $preguntaYRespuestas['pregunta'],
                'respuestas' => $preguntaYRespuestas['respuestas'],
                'categoria' => $preguntaYRespuestas['categoria'],
                'categoriaEstilo' => $estiloCategoria,
                'puntaje' => isset($_SESSION['puntaje']) ? $_SESSION['puntaje'] : 0,
            ];

            return $data;
        } else {
            Logger::info($_SESSION["errorRespuesta"] = "Ha ocurrido un error al cargar las respuestas");
            return null;
        }
    }

    public function nuevaPartida() {
        !isset($_SESSION['usuario']) ? Redirect::to('/usuario/ingresar') : null;

        $_SESSION['puntaje'] = 0;
        $data = $this->cargarPregunta();

        if ($data) {
            $this->render->printView('juego', $data);
        }
    }

    public function verificarRespuesta() {
        $esCorrecta = $_POST["esCorrecta"];

        if ($esCorrecta === "1") {
            // Respuesta correcta, incrementa el puntaje
            $_SESSION['puntaje'] += 1;
        } else{
            $_SESSION['puntaje'] = 0;
        }

        $data = $this->cargarPregunta();

        if ($data) {
            $this->render->printView('juego', $data);
        }
    }
}