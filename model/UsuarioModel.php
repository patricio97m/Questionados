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
        return $this->database->query("SELECT * FROM `usuario` WHERE BINARY usuario = '$nombreUsuario'");
    }

    public function buscarUsuarioEspecifico($nombreUsuario) {
        return $this->database->query("SELECT nombre, idusuario, nombre, apellido, fecha_nac, sexo, pais, ciudad, usuario, fotoPerfil FROM `usuario` WHERE BINARY usuario = '$nombreUsuario'");
    }

    public function verificarUsuario($nombreUsuario, $contrasena) {
        return $this->database->query("SELECT * FROM `usuario` WHERE BINARY usuario = '$nombreUsuario' && BINARY contrasena = '$contrasena' ");
    }

    public function actualizarUsuario($id_usuario, $nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen) {

    }

    public function obtenerPartidasPorUsuario($usuarioNombre) {
        $usuarioEncontrado = $this->buscarUsuarioEspecifico($usuarioNombre);
        $idUsuario = $usuarioEncontrado[0]['idusuario'];

        $queryPuntajeTotal = "SELECT SUM(puntaje_obtenido) AS puntaje_total FROM partida WHERE idusuario = '$idUsuario'";
        $resultadoPuntajeTotal = $this->database->query($queryPuntajeTotal);

        $queryUltimasPartidas = "SELECT * FROM `partida` WHERE idusuario = '$idUsuario' ORDER BY fecha_partida DESC LIMIT 10";
        $resultadoUltimasPartidas = $this->database->query($queryUltimasPartidas);

        $queryRanking = "SELECT u.usuario, SUM(p.puntaje_obtenido) AS puntaje_total
              FROM Usuario u
              INNER JOIN Partida p ON u.idUsuario = p.idUsuario
              GROUP BY u.usuario
              ORDER BY puntaje_total DESC
              LIMIT 5";
        $resultadoRankingUsuario = $this->database->query($queryRanking);

        // Crea un array con los resultados de ambas consultas
        return [
            'puntajeTotal' => (int) $resultadoPuntajeTotal[0]['puntaje_total'],
            'ultimasPartidas' => $resultadoUltimasPartidas,
            'rankingUsuario' => $resultadoRankingUsuario
        ];
    }
}