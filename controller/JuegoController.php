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
        $idUsuario = $_SESSION['usuario'][0]['idUsuario'];
        $preguntaYRespuestas = $this->model->obtenerPreguntaAlAzar($idUsuario);

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
            $horaDeLaPregunta = time();

            return [
                'usuario' => $_SESSION['usuario'],
                'pregunta' => $preguntaYRespuestas['pregunta'],
                'respuestas' => $preguntaYRespuestas['respuestas'],
                'categoria' => $preguntaYRespuestas['categoria'],
                'categoriaEstilo' => $estiloCategoria,
                'puntaje' => $_SESSION['puntaje'] ?? 0,
                'horaDeLaPregunta' => $horaDeLaPregunta,
                'tiempoRestante' => ($horaDeLaPregunta + 15) - time(),
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
            $data['tiempoRestante'] = ($data['horaDeLaPregunta'] + 15) - time();
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
        $tiempoRestante = ($_SESSION['juego_data']['horaDeLaPregunta'] + 15) - time();
        $contestoATiempo = ($tiempoRestante > 0);
        $contador_dificultad_media = $_SESSION['contador_dificultad_media'];

        if ($esCorrecta === "1" && $contestoATiempo) {
            // Respuesta correcta, incrementa el puntaje
            $_SESSION['puntaje'] += 1;
            if ($contador_dificultad_media > 10){ // Se actualiza la dificultad si se sobrepasa las 10 preguntas
                $idUsuario = $_SESSION['usuario'][0]['idUsuario']; $idPregunta = $_SESSION['idPregunta'];
                $this->model->guardarRespuestaUsuario($idUsuario, $idPregunta, 1);
                $this->model->actualizarDificultadPregunta($idPregunta);
            }
            $data = $this->cargarPregunta();
            $_SESSION['juego_data'] = $data; //Se guarda las preguntas actuales para mostar el modal por si se pierde
            Redirect::to('/juego/nuevaPartida');
        }
        else {
            $puntajeFinal = $_SESSION['puntaje'];
            $puntajeFinal = ($puntajeFinal === 0) ? $puntajeFinal . " " : $puntajeFinal; //Soluciona que no se abra el modal con el puntaje en 0

            $idUsuario = $_SESSION['usuario'][0]['idUsuario']; $idPregunta = $_SESSION['idPregunta'];
            $_SESSION['contador_dificultad_media'] = 0;
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

    public function nuevaPregunta() {
        $data['usuario'] = $_SESSION['usuario'];
        $this->render->printView('nuevaPregunta', $data);
    }
    public function procesarNuevaPregunta() {
        if($_POST){
            $pregunta = $_POST["pregunta"];
            $respuestaCorrecta = $_POST['respuestaCorrecta'];
            $respuestaIncorrecta1 = $_POST['respuestaIncorrecta1'];
            $respuestaIncorrecta2 = $_POST['respuestaIncorrecta2'];
            $respuestaIncorrecta3 = $_POST['respuestaIncorrecta3'];
            $categoria = $_POST['categoria'];
            $dificultad = $_POST['dificultad'];
            $idUsuario = $_SESSION['usuario'][0]['idUsuario'];

            $this->model->crearPregunta($pregunta, $respuestaCorrecta, $respuestaIncorrecta1, $respuestaIncorrecta2, $respuestaIncorrecta3, $categoria, $dificultad, $idUsuario);
            $_SESSION['alertaPregunta'] = "Pregunta subida con éxito. Pendiente aprobación de un administrador.";
            Redirect::to('/');
        }else{
            $_SESSION["error"] ="Cargue datos validos.";
            Redirect::to('/juego/nuevaPregunta');
        }
    }

    public function eliminarSugerencia() {
        if (!$_SESSION['usuario'][0]['esEditor']){
            Redirect::to('/');
        }

        $idPregunta = $_POST['idPregunta'];
        $this->model->eliminarSugerencia($idPregunta);
        $_SESSION["notificacionPregunta"] = "Pregunta eliminada con éxito.";
        $redigirA = $_SESSION['redirigirA'];
        unset($_SESSION['juego_data']);
        Redirect::to('/home/' . $redigirA);
    }

    public function aceptarSugerencia() {
        if (!$_SESSION['usuario'][0]['esEditor']){
            Redirect::to('/');
        }

        $redigirA = $_SESSION['redirigirA'];

        $idPregunta = $_POST['idPregunta'];
        $this->model->aceptarSugerencia($idPregunta);
        $_SESSION["notificacionPregunta"] = "Pregunta aceptada con éxito.";
        Redirect::to('/home/' . $redigirA);
    }

    public function editarPregunta() {
        if (!$_SESSION['usuario'][0]['esEditor']){
            Redirect::to('/');
        }

        $idPregunta = $_POST['idPregunta'];
        $preguntaYRespuestas = $this->model->obtenerPreguntaPorId($idPregunta);


        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'pregunta' => $preguntaYRespuestas['pregunta'],
            'respuestas' => $preguntaYRespuestas['respuestas'],
            'idPregunta' => $idPregunta,
            'redirigirA' => $_SESSION['redirigirA']
        ];
        $this->render->printView('editarPregunta', $datos);
    }

    public function actualizarPregunta() {
        if (!$_SESSION['usuario'][0]['esEditor']){
            Redirect::to('/');
        }

        $idPregunta = $_POST["idPregunta"];
        $pregunta = $_POST["pregunta"];
        $respuestaCorrecta = $_POST['respuestaCorrecta'];
        $respuestasIncorrectas = $_POST['respuestaIncorrecta'];
        $categoria = $_POST['categoria'];
        $dificultad = $_POST['dificultad'];

        $redigirA = $_SESSION['redirigirA'];
        unset($_SESSION['juego_data']);

        $this->model->actualizarPregunta($pregunta, $idPregunta,  $respuestaCorrecta, $respuestasIncorrectas, $categoria, $dificultad);
        $_SESSION["notificacionPregunta"] = "Pregunta modificada con éxito.";
        Redirect::to('/home/' . $redigirA);
    }

    public function reportarPregunta() {
        if (!isset($_SESSION['usuario'])){
            Redirect::to('/usuario/ingresar');
        }

        $motivoReporte = $_POST['motivo'];
        $idPregunta = $_SESSION['idPregunta'];
        $idUsuario = $_SESSION['usuario'][0]['idUsuario'];

        $this->model->ReportarPregunta($idPregunta, $idUsuario,  $motivoReporte);
        $_SESSION['alertaPregunta'] = "Pregunta reportada con éxito. Pendiente revisión de un administrador.";
        Redirect::to('/');
    }

    public function eliminarReporte() {
        if (!$_SESSION['usuario'][0]['esEditor']){
            Redirect::to('/');
        }

        $idPregunta = $_POST["idPregunta"];
        $_SESSION["notificacionPregunta"] = "Reporte resuelto con éxito.";
        $redigirA = $_SESSION['redirigirA'];
        $this->model->eliminarReporte($idPregunta);
        Redirect::to('/home/' . $redigirA);
    }
}