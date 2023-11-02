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
        $idUsuario = $_SESSION['usuario'][0]['idUsuario'];
        $nombreUsuarioLogueado = $_SESSION['usuario'][0]['usuario'];

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'partidasUsuario' => $this->model->obtenerPartidasPorId($idUsuario),
            'puntajeTotal' => $this->model->obtenerPuntajeTotalPorId($idUsuario),
            'rankingUsuarios' => $this->model->obtenerRankingUsuarios(),
        ];

        if(!empty($_SESSION['alertaVerificacion'])){
            $datos["alertaVerificacion"] = $_SESSION['alertaVerificacion'];
        }

        if(!empty($_SESSION['alertaPregunta'])){
            $datos["alertaPregunta"] = $_SESSION['alertaPregunta'];
            unset( $_SESSION['alertaPregunta']);
        }

        foreach ($datos['rankingUsuarios'] as &$rankingUsuario) {
            $rankingUsuario['esUsuarioLogueado'] = ($rankingUsuario['usuario'] === $nombreUsuarioLogueado);
        }

        if ($datos['usuario']){$this->render->printView('home', $datos);}
        else Redirect::to('/usuario/ingresar');
    }

    public function ranking() {
        $periodo = $_GET['periodo'] ?? 'mes';

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'ranking' => $this->model->obtenerMejoresPuntajesPorFecha($periodo),
            'periodo' => $periodo
        ];

        if ($datos['usuario']){$this->render->printView('ranking', $datos);}
        else Redirect::to('/usuario/ingresar');
    }

    public function moderarPreguntas() {
        $preguntasConRespuestas = $this->model->obtenerPreguntasAModerar();

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'preguntas' => $preguntasConRespuestas,
            'titulo' => 'Moderar nuevas sugerencias',
            'actionBtnAceptar' => 'aceptarSugerencia',
            'rolUsuario' => 'Autor'
        ];

        if (!empty($_SESSION['notificacionPregunta'])) {
            $datos["notificacionPregunta"] = $_SESSION['notificacionPregunta'];
            unset($_SESSION['notificacionPregunta']);
        }

        $_SESSION['redirigirA'] = "moderarPreguntas";

        if ($_SESSION['usuario'][0]['esEditor']) {
            $this->render->printView('editor', $datos);
        } else {
            Redirect::to('/usuario/ingresar');
        }
    }

    public function verReportes() {
        $preguntasConRespuestas = $this->model->obtenerReportes();

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'preguntas' => $preguntasConRespuestas,
            'titulo' => 'Moderar nuevos reportes',
            'actionBtnAceptar' => 'eliminarReporte',
            'rolUsuario' => 'Reportado por'
        ];

        if (!empty($_SESSION['notificacionPregunta'])) {
            $datos["notificacionPregunta"] = $_SESSION['notificacionPregunta'];
            unset($_SESSION['notificacionPregunta']);
        }

        $_SESSION['redirigirA'] = "verReportes";

        if ($_SESSION['usuario'][0]['esEditor']) {
            $this->render->printView('editor', $datos);
        } else {
            Redirect::to('/usuario/ingresar');
        }
    }

    public function verPreguntasVerificadas() {
        $preguntasConRespuestas = $this->model->obtenerPreguntasVerificadas();

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'preguntas' => $preguntasConRespuestas,
            'titulo' => 'Ver preguntas verificadas',
            'actionBtnAceptar' => false,
            'rolUsuario' => 'Autor'
        ];

        if (!empty($_SESSION['notificacionPregunta'])) {
            $datos["notificacionPregunta"] = $_SESSION['notificacionPregunta'];
            unset($_SESSION['notificacionPregunta']);
        }

        $_SESSION['redirigirA'] = "verPreguntasVerificadas";

        if ($_SESSION['usuario'][0]['esEditor']) {
            $this->render->printView('editor', $datos);
        } else {
            Redirect::to('/usuario/ingresar');
        }
    }
}