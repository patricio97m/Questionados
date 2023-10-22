<?php

class UsuarioModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen) {
        // Comprueba si $imagen no está vacío
        if (!empty($imagen['name'])) {
            $direccionImagen = $this->guardarFoto($imagen);
        } else {
            $direccionImagen = "../public/perfil_placeholder.png";
        }

        $sql = "INSERT INTO `usuario` (
        `nombre`, `apellido`, `fecha_nac`, `sexo`, `pais`, `ciudad`, `mail`, `usuario`, `contrasena`, `fotoPerfil`) 
    VALUES 
        ('$nombre', '$apellido', '$fecha_nac', '$sexo', '$pais', '$ciudad', '$mail', '$usuario', '$contrasena', '$direccionImagen');";

        Logger::info('UsuarioAlta: ' . $sql);
        $this->database->query($sql);
    }

    public function buscarUsuario($nombreUsuario) {
        return $this->database->query("SELECT * FROM `usuario` WHERE BINARY usuario = '$nombreUsuario'");
    }

    public function buscarUsuarioEspecifico($nombreUsuario) {
        return $this->database->query("SELECT idusuario, nombre, apellido, fecha_nac, sexo, pais, ciudad, usuario, fotoPerfil FROM `usuario` WHERE BINARY usuario = '$nombreUsuario'");
    }

    public function verificarUsuario($nombreUsuario, $contrasena) {
        return $this->database->query("SELECT * FROM `usuario` WHERE BINARY usuario = '$nombreUsuario' && BINARY contrasena = '$contrasena' ");
    }

    public function actualizarUsuario($usuarioViejo, $nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuarioNuevo, $contrasena, $imagen) {
        $idUsuario = $this->buscarUsuarioEspecifico($usuarioViejo)[0]["idusuario"];
        $this->database->query("UPDATE usuario
    SET
        nombre = '$nombre',
        apellido = '$apellido',
        fecha_nac = '$fecha_nac',
        sexo = '$sexo',
        pais = '$pais',
        ciudad = '$ciudad',
        mail = '$mail',
        usuario = '$usuarioNuevo',
        contrasena = '$contrasena'
    WHERE idUsuario = '$idUsuario';"
        );

        if (!empty($imagen['name'])) {
            $fotoVieja = substr($this->buscarUsuarioEspecifico($usuarioViejo)[0]["fotoPerfil"], 3);
            $fotoNueva = $this->guardarFoto($imagen);

            if (file_exists($fotoVieja)) {
                if ($fotoVieja != "public/perfil_placeholder.png") {
                    if (unlink($fotoVieja)) {
                        Logger::info("Archivo de Foto de Perfil anterior eliminado correctamente.");
                    }
                }
            }

            $this->database->query("UPDATE usuario
        SET
            fotoPerfil = '$fotoNueva'
        WHERE idUsuario = '$idUsuario';"
            );
            Logger::info("Archivo de Foto de Perfil actualizada correctamente.");
        }
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

    public function guardarFoto($imagen){
        $pathImagenes = "public/fotosPerfil/";
        $extensionDelArchivo = pathinfo(basename($imagen["name"]), PATHINFO_EXTENSION);
        $numeroRandom = rand(1,100000);
        $destinoArchivo = $pathImagenes . $numeroRandom . "." . $extensionDelArchivo;

        while(file_exists($destinoArchivo)){
            $numeroRandom = rand(1,100000);
            $destinoArchivo = $pathImagenes . $numeroRandom . "." . $extensionDelArchivo;
        }
        if(move_uploaded_file($imagen["tmp_name"], $destinoArchivo)) {
            $destinoArchivo = "../" . $destinoArchivo;
            return $destinoArchivo;
        }
        else{
            $_SESSION["errorAlta"] = "Ha ocurrido un error al cargar la Foto de Perfil";
        }
    }
    
        
}