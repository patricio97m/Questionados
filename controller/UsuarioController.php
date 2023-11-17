<?php
include_once("helper/Logger.php");
include_once("third-party/phpqrcode/qrlib.php");
class UsuarioController
{
    private $render;
    private $model;
    private $mapsAPI = 'AIzaSyBu6BZ-GE8v1mrSPNMf7JYYGI7KaHhnBqU';
    public function __construct($render, $model) {
        $this->render = $render;
        $this->model = $model;
    }
    public function registro(){
        $this->redirigirSiUsuarioLogueado();
        $data = [];
        $data['mapsAPI'] = $this->mapsAPI;

        $this->setDatosError($data);

        $this->render->printView('registro', $data);
    }

    public function procesarUsuario(){

        if($_POST){
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
                $_SESSION["error"] = "Las contraseñas no coinciden.";
                Redirect::to('/usuario/registro');
            }
            if ($usuarioExistente) {
                $_SESSION["error"] ="El nombre de usuario ya existe.";
                Redirect::to('/usuario/registro');
            }
            if (empty($pais) || empty($ciudad)) {
                $_SESSION["error"] ="Ingrese una ubicación válida.";
                Redirect::to('/usuario/registro');
            }
            else {
                $_SESSION['error'] = "Chequea tu bandeja de correo y verificá tu cuenta!";
                $_SESSION["modal"] = "$mail";
                $this->model->crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen);
                $this->model->enviarCorreoVerificacion($mail, $nombre, $usuario);
                Redirect::to('/usuario/ingresar');
            }
        }else{
            $_SESSION["error"] ="Cargue datos validos.";
            Redirect::to('/usuario/registro');
        }
    }

    public function ingresar(){
        $this->redirigirSiUsuarioLogueado();
        $data = [];

        $this->setDatosError($data);

        $this->render->printView('ingresar', $data);
    }

    public function procesarIngreso(){
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        $usuarioEncontrado  = $this->model->loguearUsuario($usuario, $contrasena);

        if ($usuarioEncontrado) {
            if($usuarioEncontrado[0]['estaVerificado']){
                $_SESSION['usuario'] = $usuarioEncontrado;
                Redirect::to('/');
            }
            else{
                $_SESSION['error'] = "Chequea tu bandeja de correo y verificá tu cuenta!";
                Redirect::to('/usuario/ingresar');
            }
            
        } else {
            $_SESSION["error"] ="Usuario o contraseña incorrectos.";
            Redirect::to('/usuario/ingresar');
        }
    }

    public function perfil(){
        $data['usuario'] = $_SESSION['usuario'];
        $data['editable'] = "disabled";
        $data['cambiarFoto'] = false;
        $data['modificaDatos'] = true;
        $data['actualizarDatos'] = false;
        $data['mapsAPI'] = $this->mapsAPI;

        $this->setDatosError($data);
        if(!empty($_SESSION['mensajeExito'])){
            $data["exito"] = $_SESSION['mensajeExito'];
            unset( $_SESSION['mensajeExito']);
        }
        $this->setDatosError($data);

        if ($data['usuario']){$this->render->printView('perfil', $data);}
        else Redirect::to('/usuario/ingresar');
    }

    public function editar(){
        $data['usuario'] = $_SESSION['usuario'];
        $data['editable'] = "";
        $data['cambiarFoto'] = true;
        $data['modificaDatos'] = false;
        $data['actualizarDatos'] = true;
        $data['mapsAPI'] = $this->mapsAPI;

        if ($data['usuario']){$this->render->printView('perfil', $data);}
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

    private function setDatosError(&$data) {
        if(!empty($_SESSION['error'])){
            $data["error"] = $_SESSION['error'];
            unset( $_SESSION['error']);
        }
        if(!empty($_SESSION['modal'])){
            $data["modal"] = $_SESSION['modal'];
            unset( $_SESSION['modal']);
        }
        if(!empty($_SESSION['alertaVerificacion'])){
            $data["alertaVerificacion"] = $_SESSION['alertaVerificacion'];
        }
    }
    public function actualizarUsuario() {
        $nombre = $_POST["nombre"];
        $apellido = $_POST['apellido'];
        $fecha_nac = $_POST['fecha_nac'];
        $sexo = $_POST['sexo'];
        $pais = $_POST['pais'];
        $ciudad = $_POST['ciudad'];
        $mail = $_POST['mail'];
        $usuarioViejo = $_SESSION['usuario'][0]['usuario'];
        $usuarioNuevo = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        if (empty($pais) || empty($ciudad)) {
            $_SESSION["error"] ="Ingrese una ubicación válida.";
            Redirect::to('/usuario/perfil');
        }

        if(isset($_FILES["foto_perfil"])){
            $imagen = $_FILES["foto_perfil"];
        }

        if($usuarioNuevo != $usuarioViejo){
            $usernameEnUso = $this->model->buscarUsuario($usuarioNuevo);
            if ($usernameEnUso) {
                $_SESSION["error"] = "El nombre de usuario ya existe.";
                Redirect::to('/usuario/perfil');
            } else {
                $this->model->actualizarUsuario($usuarioViejo, $nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuarioNuevo, $contrasena, $imagen);
                unset($_SESSION["usuario"]);
                Redirect::to('/usuario/ingresar');
            }
        }
        else {
            $this->model->actualizarUsuario($usuarioViejo, $nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuarioNuevo, $contrasena, $imagen);
            $usuarioEncontrado  = $this->model->loguearUsuario($usuarioNuevo, $contrasena);
            $_SESSION['usuario'] = $usuarioEncontrado;
            $_SESSION['mensajeExito'] = "Usuario modificado correctamente.";
            Redirect::to('/usuario/perfil');
        }
        
    }

    public function datosUsuario() {
        $usuarioNombre = $_GET['nombre'];
        $partidas['partidas'] = $this->model->obtenerPartidasPorUsuario($usuarioNombre);
        QRcode::png('http://localhost/usuario/datosUsuario?nombre='.$_GET['nombre'], './public/qr/qr_'.$_GET['nombre'].'.png',QR_ECLEVEL_H,4);

        $datos = [
            'usuario' => $_SESSION['usuario'][0],
            'usuarioEncontrado' => $this->model->buscarUsuario($usuarioNombre),
            'partidas' => $partidas['partidas']['ultimasPartidas'],
            'puntajeTotal' => $partidas['partidas']['puntajeTotal'],
            'rankingUsuarios' => $partidas['partidas']['rankingUsuario'],
            'qrUsuario' => '../public/qr/qr_'.$_GET['nombre'].'.png',
            'mapsAPI' => $this->mapsAPI
        ];

        foreach ($datos['rankingUsuarios'] as &$rankingUsuario) {
            $rankingUsuario['esUsuarioLogueado'] = ($rankingUsuario['usuario'] === $usuarioNombre);
        }

        $this->render->printView('datosUsuario', $datos);
    }    
    
    public function verificarUsuario(){
       if(isset($_GET['usuario']) && isset($_GET['codigoVerificacion'])){
        $username = $_GET['usuario'];
        $codigoVerificacion = $_GET['codigoVerificacion'];
        if($this->model->verificarUsuario($username, $codigoVerificacion)){
            unset($_SESSION['error']);
        }
       }
       
       Redirect::to('/');
    }

}