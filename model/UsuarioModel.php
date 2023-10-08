<?php

class UsuarioModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen) {
        $pathImagenes = "public/fotosPerfil/";
        $extensionDelArchivo = pathinfo(basename($imagen["name"]), PATHINFO_EXTENSION);
        $destinoArchivo = $pathImagenes . $usuario . "." . $extensionDelArchivo;

        if(move_uploaded_file($imagen["tmp_name"], $destinoArchivo)) {
            $destinoArchivo = "../" . $destinoArchivo;
            $sql = "INSERT INTO `usuario` (
                `nombre`, `apellido`, `fecha_nac`, `sexo`, `pais`, `ciudad`, `mail`, `usuario`, `contrasena`, `fotoPerfil`) 
            VALUES 
                ('$nombre', '$apellido', '$fecha_nac', '$sexo', '$pais', '$ciudad', '$mail', '$usuario', '$contrasena', '$destinoArchivo');";
            Logger::info('UsuarioAlta: ' . $sql);
            $this->database->query($sql);
        }else {
            Logger::info($_SESSION["errorAlta"] = "Ha ocurrido un error al cargar la Foto de Perfil");
        }
    }

    public function buscarUsuario($nombreUsuario) {
        $usuario = $this->database->query("SELECT * FROM `usuario` WHERE usuario LIKE '%$nombreUsuario%'");
        return $usuario;
    }

    public function verificarUsuario($nombreUsuario, $contrasena) {
        $usuario = $this->database->query("SELECT * FROM `usuario` WHERE usuario LIKE '%$nombreUsuario%' && contrasena LIKE '%$contrasena%' ");
        return $usuario;
    }
}