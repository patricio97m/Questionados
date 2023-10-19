<?php

class UsuarioController
{
    private $render;
    private $model;

    public function __construct($render, $model) {
        $this->render = $render;
        $this->model = $model;
    }
    public function registro(){
        $this->redirigirSiUsuarioLogueado();
        $data = [];

        if(!empty($_SESSION['error'])){
            $data["error"] = $_SESSION['error'];
            unset( $_SESSION['error']);
        }

        $this->render->printView('registro', $data);
    }

    public function procesarUsuario(){
        $nombre = $_POST["nombre"];
        $apellido = $_POST['apellido'];
        $fecha_nac = $_POST['fecha_nac'];
        $sexo = $_POST['sexo'];
        $pais = $_POST['pais'];
        $ciudad = $_POST['ciudad'];
        $mail = $_POST['mail'];
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];
        $repetirContrasena = $_POST['repetirContrasena'];
        $imagen = $_FILES["foto_perfil"];

        $usuarioExistente = $this->model->buscarUsuario($usuario);

        if ($contrasena !== $repetirContrasena) {
            $this->setSessionError("Las contraseñas no coinciden.");
            Redirect::to('/usuario/registro');
        }
        if ($usuarioExistente) {
            $this->setSessionError("El nombre de usuario ya existe.");
            Redirect::to('/usuario/registro');
        } else {
            $_SESSION["modal"] = "$mail";
            $this->model->crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen);
            Redirect::to('/usuario/ingresar');
        }
    }

    public function ingresar(){
        $this->redirigirSiUsuarioLogueado();
        $data = [];

        if(!empty($_SESSION['error'])){
            $data["error"] = $_SESSION['error'];
            unset( $_SESSION['error']);
        }
        if(!empty($_SESSION['modal'])){
            $data["modal"] = $_SESSION['modal'];
            unset( $_SESSION['modal']);
        }

        $this->render->printView('ingresar', $data);
    }

    public function procesarIngreso(){
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        $usuarioEncontrado  = $this->model->verificarUsuario($usuario, $contrasena);

        if ($usuarioEncontrado) {
            $_SESSION['usuario'] = $usuarioEncontrado;
            Redirect::to('/');
        } else {
            $this->setSessionError("Usuario o contraseña incorrectos.");
            Redirect::to('/usuario/ingresar');
        }
    }

    public function perfil(){
        $datosUsuario['usuario'] = $_SESSION['usuario'];
        if ($datosUsuario['usuario']){$this->render->printView('perfil', $datosUsuario);}
        else Redirect::to('/usuario/ingresar');
    }

    public function cerrarSesion(){
        session_destroy();
        Redirect::to('/usuario/ingresar');
    }

    private function redirigirSiUsuarioLogueado() {
        if (isset($_SESSION['usuario'])) {
            Redirect::to('/');
        }
    }

    private function setSessionError($mensaje) {
        $_SESSION["error"] = $mensaje;
    }

    public function actualizarUsuario() {
    }

    public function datosUsuario() {
        $usuarioNombre = $_GET['nombre'];
        $partidas['partidas'] = $this->model->obtenerPartidasPorUsuario($usuarioNombre);

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'usuarioEncontrado' => $this->model->buscarUsuarioEspecifico($usuarioNombre),
            'partidas' => $partidas['partidas']['ultimasPartidas'],
            'puntajeTotal' => $partidas['partidas']['puntajeTotal'],
            'rankingUsuarios' => $partidas['partidas']['rankingUsuario'],
        ];

        foreach ($datos['rankingUsuarios'] as &$rankingUsuario) {
            $rankingUsuario['esUsuarioLogueado'] = ($rankingUsuario['usuario'] === $usuarioNombre);
        }

        $this->render->printView('datosUsuario', $datos);
    }

}