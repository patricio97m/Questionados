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
        $preguntaYRespuestas = $this->model->obtenerPreguntaAlAzar();

        if ($preguntaYRespuestas) { //Array para comparar valores de las categorías y elegir el estilo correcto
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

            return [
                'usuario' => $_SESSION['usuario'],
                'pregunta' => $preguntaYRespuestas['pregunta'],
                'respuestas' => $preguntaYRespuestas['respuestas'],
                'categoria' => $preguntaYRespuestas['categoria'],
                'categoriaEstilo' => $estiloCategoria,
                'puntaje' => $_SESSION['puntaje'] ?? 0,
            ];
        } else {
            Logger::info($_SESSION["errorRespuesta"] = "Ha ocurrido un error al cargar las respuestas");
            return null;
        }
    }

    public function nuevaPartida() {
        !isset($_SESSION['usuario']) ? Redirect::to('/usuario/ingresar') : null;
        unset($_SESSION['modal']);

        $_SESSION['puntaje'] = 0;
        $data = $this->cargarPregunta();
        $_SESSION['juego_data'] = $data;

        $this->render->printView('juego', $data);

    }

    public function verificarRespuesta() {
        $esCorrecta = $_POST["esCorrecta"];

        if ($esCorrecta === "1") {
            // Respuesta correcta, incrementa el puntaje
            $_SESSION['puntaje'] += 1;
            $data = $this->cargarPregunta();
            $_SESSION['juego_data'] = $data; //Se guarda las preguntas actuales para mostar el modal por si se pierde
        }
        else {
            $puntajeFinal = $_SESSION['puntaje'];

            $data = $_SESSION['juego_data'];
            $data['modal'] = "$puntajeFinal";
        }

        $this->render->printView('juego', $data);
    }
}