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
        $preguntaYRespuestas = $this->model->obtenerPreguntaAlAzar($_SESSION['usuario'][0]['idUsuario']);

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
            $_SESSION['idPregunta'] = $preguntaYRespuestas['idPregunta'];

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
        if (!isset($_SESSION['usuario'])) {
            Redirect::to('/usuario/ingresar');
            return;
        }

        // Verifica si hay una partida en curso
        if (isset($_SESSION['juego_data']) && !empty($_SESSION['juego_data'])) {
            $data = $_SESSION['juego_data'];
        } else {
            // Si no hay una partida en curso, inicia una nueva partida
            unset($_SESSION['modal']);
            unset($_SESSION['preguntas_utilizadas']);
            $_SESSION['puntaje'] = 0;
            $data = $this->cargarPregunta();
            $_SESSION['juego_data'] = $data;
        }
        $this->render->printView('juego', $data);
    }

    public function verificarRespuesta() {
        $esCorrecta = $_POST["esCorrecta"];

        if ($esCorrecta === "1") {
            // Respuesta correcta, incrementa el puntaje
            $_SESSION['puntaje'] += 1;
            $idUsuario = $_SESSION['usuario'][0]['idUsuario']; $idPregunta = $_SESSION['idPregunta'];
            $this->model->guardarRespuestaUsuario($idUsuario, $idPregunta, 1);
            $this->model->actualizarDificultadPregunta($idPregunta);
            $data = $this->cargarPregunta();
            $_SESSION['juego_data'] = $data; //Se guarda las preguntas actuales para mostar el modal por si se pierde
        }
        else {
            $puntajeFinal = $_SESSION['puntaje'];
            $puntajeFinal = ($puntajeFinal === 0) ? $puntajeFinal . " " : $puntajeFinal; //Soluciona que no se abra el modal con el puntaje en 0

            $idUsuario = $_SESSION['usuario'][0]['idUsuario']; $idPregunta = $_SESSION['idPregunta'];
            $this->model->guardarRespuestaUsuario($idUsuario, $idPregunta, 0);
            $this->model->actualizarDificultadPregunta($idPregunta);
            $data = $_SESSION['juego_data'];
            unset($_SESSION['juego_data']);
            $this->guardarPartida($puntajeFinal);
            $data['modal'] = "$puntajeFinal";
        }

        $this->render->printView('juego', $data);
    }

    private function guardarPartida($puntajeFinal) {
        $idUsuario = $_SESSION['usuario'][0]['idUsuario'];
        $this->model->guardarPartidaEnBD($idUsuario, $puntajeFinal);

    }
}