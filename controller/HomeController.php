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

    public function rankingAjax() {
        $periodo = $_GET['periodo'] ?? 'mes';
        $ranking = $this->model->obtenerMejoresPuntajesPorFecha($periodo);

        echo json_encode($ranking);
    }

    public function cantidadJugadoresAjax() {
        $usuarios = array_merge($this->model->obtenerCantidadJugadoresPorFecha('historico'),$this->model->obtenerCantidadJugadoresPorFecha('mes'),$this->model->obtenerCantidadJugadoresPorFecha('semana'),$this->model->obtenerCantidadJugadoresPorFecha('dia'));
        echo json_encode($usuarios);
    }

    public function cantidadPartidasAjax() {
        $partidas = array_merge($this->model->obtenerCantidadPartidasPorFecha('historico'),$this->model->obtenerCantidadPartidasPorFecha('mes'),$this->model->obtenerCantidadPartidasPorFecha('semana'),$this->model->obtenerCantidadPartidasPorFecha('dia'));
        echo json_encode($partidas);
    }

    public function cantidadPreguntasAjax() {
        $preguntas = array_merge($this->model->obtenerCantidadPreguntasPorFecha('historico'),$this->model->obtenerCantidadPreguntasPorFecha('mes'),$this->model->obtenerCantidadPreguntasPorFecha('semana'),$this->model->obtenerCantidadPreguntasPorFecha('dia'));
        echo json_encode($preguntas);
    }
    public function usuariosPorSexo() {
        $usuarios = array_merge($this->model->obtenerUsuariosPorSexo());
        echo json_encode($usuarios);
    }
    public function usuariosPorEdad() {
        $usuarios = array_merge($this->model->obtenerUsuariosPorEdad());
        echo json_encode($usuarios);
    }
    public function usuariosPorPais() {
        $usuarios = array_merge($this->model->obtenerUsuariosPorPais());
        echo json_encode($usuarios);
    }
    public function usuariosPorPorcentajeDePreguntas() {
        $usuarios = array_merge($this->model->obtenerUsuariosPorPorcentajeDePreguntas());
        echo json_encode($usuarios);
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

    public function verCategorias() {
        $categorias = $this->model->obtenerCategorias();

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'categorias' => $categorias,
            'titulo' => 'Ver categorías',
            'actionBtnAceptar' => false,
            'rolUsuario' => 'Autor'
        ];

        if (!empty($_SESSION['error'])) {
            $datos["error"] = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        if (!empty($_SESSION['confirmacion'])) {
            $datos["confirmacion"] = $_SESSION['confirmacion'];
            unset($_SESSION['confirmacion']);
        }

        $_SESSION['redirigirA'] = "verCategorias";

        if ($_SESSION['usuario'][0]['esEditor']) {
            $this->render->printView('verCategorias', $datos);
        } else {
            Redirect::to('/usuario/ingresar');
        }
    }

    public function estadisticas() {

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
        ];

        if ($datos['usuario']){$this->render->printView('estadisticas', $datos);}
        else Redirect::to('/usuario/ingresar');
    }
}