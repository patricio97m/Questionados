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
            $_SESSION["error"] = "Las contraseñas no coinciden";
            Redirect::to('/usuario/registro');
        }
        if ($usuarioExistente) {
            $_SESSION["error"] = "El nombre de usuario ya existe ";
            Redirect::to('/usuario/registro');
        } else {
            $this->model->crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen);
            Redirect::to('/usuario/ingresar');
        }
    }

    public function ingresar(){
        $data = [];

        if(!empty($_SESSION['error'])){
            $data["error"] = $_SESSION['error'];
            unset( $_SESSION['error']);
        }

        $this->render->printView('ingresar', $data);
    }

    public function procesarIngreso(){
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        $usuarioEncontrado  = $this->model->verificarUsuario($usuario, $contrasena);

        if ($usuarioEncontrado) {
            $_SESSION['usuario'] = $usuarioEncontrado;
            Redirect::to('/usuario/perfil');
        } else {
            $_SESSION["error"] = "Usuario o contraseña incorrectos";
            Redirect::to('/usuario/ingresar');
        }
    }

    public function perfil(){
        $datosUsuario['usuario'] = $_SESSION['usuario'];
        $this->render->printView('perfil', $datosUsuario);
    }

}