<?php

class UsuarioModel
{
    private $database;
    private $mailer;

    public function __construct($database, $mailer) {
        $this->database = $database;
        $this->mailer = $mailer;
    }

    public function crearUsuario($nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuario, $contrasena, $imagen) {
        // Comprueba si $imagen no está vacío
        if (!empty($imagen['name'])) {
            $destino = "public/fotosPerfil/";
            $direccionImagen = $this->guardarFoto($imagen, $destino);
        } else {
            $direccionImagen = "../public/perfil_placeholder.png";
        }

        $sql = "INSERT INTO `usuario` (
        `nombre`, `apellido`, `fecha_nac`, `sexo`, `pais`, `ciudad`, `mail`, `usuario`, `contrasena`, `estaVerificado`, `fotoPerfil`) 
    VALUES 
        ('$nombre', '$apellido', '$fecha_nac', '$sexo', '$pais', '$ciudad', '$mail', '$usuario', '$contrasena', false, '$direccionImagen');";

        Logger::info('UsuarioAlta: ' . $sql);
        $this->database->query($sql);
    }

    public function buscarUsuario($nombreUsuario) {
        return $this->database->query("SELECT * FROM `usuario` WHERE usuario = '$nombreUsuario'");
    }

    public function loguearUsuario($nombreUsuario, $contrasena) {
        return $this->database->query("SELECT * FROM `usuario` WHERE usuario = '$nombreUsuario' && BINARY contrasena = '$contrasena' ");
    }

    public function actualizarUsuario($usuarioViejo, $nombre, $apellido, $fecha_nac, $sexo, $pais, $ciudad, $mail, $usuarioNuevo, $contrasena, $imagen) {
        $usuarioEncontrado = $this->buscarUsuario($usuarioViejo);
        $idUsuario = $usuarioEncontrado[0]['idUsuario'];

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
            $fotoVieja = substr($this->buscarUsuario($usuarioViejo)[0]["fotoPerfil"], 3);
            $destino = "public/fotosPerfil/";
            $fotoNueva = $this->guardarFoto($imagen, $destino);

            if (file_exists($fotoVieja)) {
                if ($fotoVieja != "public/perfil_placeholder.png") {
                    unlink($fotoVieja);
                }
            }

            $this->database->query("UPDATE usuario
        SET
            fotoPerfil = '$fotoNueva'
        WHERE idUsuario = '$idUsuario';"
            );
        }
    }

    public function obtenerPartidasPorUsuario($usuarioNombre) {
        $usuarioEncontrado = $this->buscarUsuario($usuarioNombre);
        $idUsuario = $usuarioEncontrado[0]['idUsuario'];

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

    public function guardarFoto($imagen, $destino){
        $extensionDelArchivo = pathinfo(basename($imagen["name"]), PATHINFO_EXTENSION);
        $numeroRandom = rand(1,100000);
        $destinoArchivo = $destino . $numeroRandom . "." . $extensionDelArchivo;

        while(file_exists($destinoArchivo)){
            $numeroRandom = rand(1,100000);
            $destinoArchivo = $destino . $numeroRandom . "." . $extensionDelArchivo;
        }
        if(move_uploaded_file($imagen["tmp_name"], $destinoArchivo)) {
            $destinoArchivo = "../" . $destinoArchivo;
            return $destinoArchivo;
        }
        else{
            $_SESSION["errorAlta"] = "Ha ocurrido un error al cargar la Foto";
        }
    }

    public function verificarUsuario($username, $codigoRecibido){
        $usuario = $this->buscarUsuario($username);
        $codigoVerificacion = $usuario[0]['codigoVerificacion'];
        if($codigoVerificacion == $codigoRecibido){
            $idUsuario = $usuario[0]['idUsuario'];
            $this->database->query("UPDATE usuario
            SET estaVerificado = true
            WHERE idUsuario = '$idUsuario';");
            return true;
        }
        else return false;
    }

    public function enviarCorreoVerificacion($mail, $nombre, $usuario){
        $codigoVerificacion = rand(100000, 999999);
        $this->guardarCodigoVerificacion($usuario, $codigoVerificacion);

        try{
            $this->mailer->setFrom('info@questionados.com.ar', 'Questionados');
            $this->mailer->addAddress($mail, $nombre);     //Add a recipient
            $this->mailer->addReplyTo('info@questionados.com.ar', 'Questionados');

            $this->mailer->isHTML(true);                                  //Set email format to HTML
            $this->mailer->Subject = 'Mail de registro en Questionados';
            $this->mailer->Body    = '<b>¡Debe verificar su cuenta!<b> <br>
                                    Para hacerlo, clickee en el siguiente link: <br> 
                                    http://localhost/usuario/verificarUsuario/codigoVerificacion=' . $codigoVerificacion . "&usuario=" . $usuario;

            $this->mailer->send();
        } catch (Exception $e) {
            echo "El mensaje no pudo ser enviado: {$this->mailer->ErrorInfo}";
        }
    }

    public function guardarCodigoVerificacion($username, $codigoVerificacion){
        $usuario = $this->buscarUsuario($username);
        $idUsuario = $usuario[0]['idUsuario'];
        $this->database->query("UPDATE usuario
        SET codigoVerificacion = $codigoVerificacion
        WHERE idUsuario = '$idUsuario';");
    }
}