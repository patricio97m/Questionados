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
            'rankingUsuarios' => $this->model->obtenerRankingUsuarios()
        ];

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
}