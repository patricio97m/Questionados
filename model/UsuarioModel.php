<?php

class UsuarioModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena) {
        $sql = "INSERT INTO `usuario` (
                   `nombre`, `apellido`, `fecha_nac`, `sexo`, `pais`, `ciudad`, `mail`, `usuario`, `contrasena` ) 
        VALUES 
            ( '$nombre', '$apellido', '$fecha_nac', '$sexo', '$pais', '$ciudad', '$mail', '$usuario', '$contrasena');";
        Logger::info('UsuarioAlta: ' . $sql);

        $this->database->query($sql);
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