<?php

class JuegoModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPreguntaAlAzar($idUsuario)
    {
        // Se obtienen las preguntas usadas en la sesión
        $preguntasUtilizadas = $_SESSION['preguntas_utilizadas'] ?? [];
        $dificultadUsuario = $this->calcularDificultadUsuario($idUsuario);

        $preguntasDificultadMedia = 10; // Número de preguntas de dificultad media
        $contadorDificultadMedia = $_SESSION['contador_dificultad_media'] ?? 0;

        if ($contadorDificultadMedia < $preguntasDificultadMedia) {
            // Se calcula si se respondieron las primeras 10 preguntas medias
            $dificultadUsuario = 'media';
            Logger::info("cant preguntas medias: " . $contadorDificultadMedia);
        }

        Logger::info("Dificultad del usuario: " . $dificultadUsuario);

        // Verificar si la dificultad del usuario existe en la tabla Pregunta
        $verificarDificultadQuery = "SELECT COUNT(*) as existe_dificultad FROM Pregunta WHERE dificultad = '$dificultadUsuario' AND esVerificada = true";
        $result = $this->database->query($verificarDificultadQuery);

        if ($result && $result[0]['existe_dificultad'] > 0) {
            // Si la dificultad del usuario existe, intenta obtener una pregunta de esa dificultad
            $baseQuery = "SELECT idPregunta, pregunta, idCategoria, dificultad FROM Pregunta WHERE dificultad = '$dificultadUsuario' AND esVerificada = true";
            Logger::info("pregunta de la misma dificultad");

            if (!empty($preguntasUtilizadas)) {
                // Si hay preguntas utilizadas, excluirlas de la consulta
                $baseQuery .= " AND idPregunta NOT IN (" . implode(',', $preguntasUtilizadas) . ")";
            }

            $baseQuery .= " ORDER BY RAND() LIMIT 1";
            $pregunta = $this->database->query($baseQuery);

            if ($pregunta) {
                if ($dificultadUsuario === 'media') { // Se verifica que las 10 primeras preguntas sean medias y se aumenta la cantidad en 1
                    $contadorDificultadMedia++;
                    $_SESSION['contador_dificultad_media'] = $contadorDificultadMedia;
                }
                $preguntasUtilizadas[] = $pregunta[0]['idPregunta'];
                $_SESSION['preguntas_utilizadas'] = $preguntasUtilizadas;

                // Obtener las respuestas de esa pregunta al azar
                $respuestasQuery = "SELECT idRespuesta, respuesta, esCorrecta FROM Respuesta WHERE idPregunta = " . $pregunta[0]['idPregunta'] . " ORDER BY RAND()";
                $respuestas = $this->database->query($respuestasQuery);

                // Obtener datos de la categoría
                $categoriaQuery = "SELECT nombre, color FROM Categoria WHERE idCategoria = " . $pregunta[0]['idCategoria'];
                $categoria = $this->database->query($categoriaQuery);

                // Combinar la pregunta con sus respuestas y su categoria
                return [
                    'pregunta' => $pregunta[0]['pregunta'],
                    'idPregunta' => $pregunta[0]['idPregunta'],
                    'dificultad' => $pregunta[0]['dificultad'],
                    'color' => $categoria[0]['color'],
                    'categoria' => $categoria[0]['nombre'],
                    'respuestas' => $respuestas
                ];
            }
        }

        // Si no se encuentra una pregunta de la misma dificultad, busca cualquier pregunta no utilizada sin restricciones de dificultad
        $baseQuery = "SELECT idPregunta, pregunta, idCategoria, dificultad FROM Pregunta WHERE esVerificada = true";
        if (!empty($preguntasUtilizadas)) {
            // Si hay preguntas utilizadas, se excluyen de la consulta
            $baseQuery .= " AND idPregunta NOT IN (" . implode(',', $preguntasUtilizadas) . ")";
        }
        $baseQuery .= " ORDER BY RAND() LIMIT 1";
        $pregunta = $this->database->query($baseQuery);
        Logger::info("pregunta de dificultad diferente.");

        if ($pregunta) {
            if ($dificultadUsuario === 'media') {
                $contadorDificultadMedia++;
                $_SESSION['contador_dificultad_media'] = $contadorDificultadMedia;
            }
            $preguntasUtilizadas[] = $pregunta[0]['idPregunta'];
            $_SESSION['preguntas_utilizadas'] = $preguntasUtilizadas;

            // Obtener las respuestas de esa pregunta al azar
            $respuestasQuery = "SELECT idRespuesta, respuesta, esCorrecta FROM Respuesta WHERE idPregunta = " . $pregunta[0]['idPregunta'] . " ORDER BY RAND()";
            $respuestas = $this->database->query($respuestasQuery);

            // Obtener datos de la categoría
            $categoriaQuery = "SELECT nombre, color FROM Categoria WHERE idCategoria = " . $pregunta[0]['idCategoria'];
            $categoria = $this->database->query($categoriaQuery);

            // Combinar la pregunta con sus respuestas
            return [
                'pregunta' => $pregunta[0]['pregunta'],
                'idPregunta' => $pregunta[0]['idPregunta'],
                'dificultad' => $pregunta[0]['dificultad'],
                'color' => $categoria[0]['color'],
                'categoria' => $categoria[0]['nombre'],
                'respuestas' => $respuestas
            ];
        }

        // Si no se encuentra ninguna pregunta disponible, reinicia las preguntas y busca nuevamente
        $_SESSION['preguntas_utilizadas'] = [];
        return $this->obtenerPreguntaAlAzar($idUsuario);
    }

    private function obtenerCantidadTotalDePreguntas() {
        $query = "SELECT COUNT(*) as total FROM Pregunta";
        $result = $this->database->query($query);

        if ($result && isset($result[0]['total'])) {
            return $result[0]['total'];
        }

        return 0;
    }
    public function guardarPartidaEnBD($idUsuario, $puntaje){
        $sql = "INSERT INTO `partida` (`puntaje_obtenido`, `fecha_partida`, `idUsuario`) 
            VALUES ('$puntaje', NOW(), '$idUsuario');";
        $this->database->query($sql);
    }

    public function guardarRespuestaUsuario($idUsuario, $idPregunta, $esCorrecta){
        $sql = "INSERT INTO `RespuestasUsuario` (`idUsuario`, `idPregunta`, `esCorrecta`) 
            VALUES ('$idUsuario', '$idPregunta', '$esCorrecta');";
        $this->database->query($sql);
    }
    public function calcularDificultadPregunta($idPregunta)
    {
        // Consulta para contar respuestas correctas e incorrectas
        $query = "SELECT 
            SUM(CASE WHEN esCorrecta = 1 THEN 1 ELSE 0 END) as respuestas_correctas,
            COUNT(*) as total_respuestas
          FROM RespuestasUsuario
          WHERE idPregunta = $idPregunta";

        $result = $this->database->query($query);

        if ($result) {
            $respuestasCorrectas = $result[0]['respuestas_correctas'];
            $totalRespuestas = $result[0]['total_respuestas'];

            if ($totalRespuestas == 0) {
                return "facil"; // No hay respuestas en la tabla
            }

            $porcentajeCorrectas = ($respuestasCorrectas / $totalRespuestas) * 100;

            if ($porcentajeCorrectas <= 30) {
                return "dificil";
            } elseif ($porcentajeCorrectas < 70) {
                return "medio";
            } else {
                return "facil";
            }
        }
        return "facil";
    }

    public function calcularDificultadUsuario($idUsuario)
    {
        // Consulta para contar las respuestas correctas e incorrectas del usuario
        $query = "SELECT 
            SUM(CASE WHEN esCorrecta = 1 THEN 1 ELSE 0 END) as respuestas_correctas,
            COUNT(*) as total_respuestas
          FROM RespuestasUsuario
          WHERE idUsuario = $idUsuario";

        $result = $this->database->query($query);

        if ($result) {
            $respuestasCorrectas = $result[0]['respuestas_correctas'];
            $totalRespuestas = $result[0]['total_respuestas'];

            if ($totalRespuestas == 0) {
                return "facil"; // El usuario no ha respondido preguntas
            }

            $porcentajeCorrectas = ($respuestasCorrectas / $totalRespuestas) * 100;

            if ($porcentajeCorrectas >= 70) {
                return "dificil";
            } elseif ($porcentajeCorrectas >= 30) {
                return "medio";
            } else {
                return "facil";
            }
        }
        return "facil";
    }
    public function actualizarDificultadPregunta($idPregunta)
    {
        // Llama a la función para calcular la dificultad de la pregunta
        $dificultad = $this->calcularDificultadPregunta($idPregunta);

        $updateQuery = "UPDATE Pregunta SET dificultad = '$dificultad' WHERE idPregunta = $idPregunta";
        $this->database->query($updateQuery);

        Logger::info("La dificultad de la pregunta " . $idPregunta . " ahora es ". $dificultad);
        return $dificultad;
    }

    public function crearPregunta($pregunta, $respuestaCorrecta, $respuestaIncorrecta1, $respuestaIncorrecta2, $respuestaIncorrecta3, $categoria, $dificultad, $idUsuario) {
        $sql = "INSERT INTO Pregunta (pregunta, idCategoria, dificultad, fecha_pregunta, idUsuario, esVerificada) 
            VALUES ('$pregunta', '$categoria', '$dificultad', NOW(), $idUsuario, false)";
        $this->database->query($sql);

        $idPreguntaArray = $this->database->query("SELECT idPregunta FROM Pregunta ORDER BY idPregunta DESC LIMIT 1");
        $idPregunta = $idPreguntaArray[0]['idPregunta'];

        $sql = "INSERT INTO Respuesta (idPregunta, respuesta, esCorrecta) 
            VALUES ($idPregunta, '$respuestaCorrecta', 1),
                   ($idPregunta, '$respuestaIncorrecta1', 0),
                   ($idPregunta, '$respuestaIncorrecta2', 0),
                   ($idPregunta, '$respuestaIncorrecta3', 0)";
        $this->database->query($sql);
        Logger::info('PreguntaAlta: ' . $sql);
    }

    public function eliminarSugerencia($idPregunta){
        $sql = "DELETE FROM Respuesta WHERE idPregunta = '$idPregunta'";
        $this->database->query($sql);

        $sql = "DELETE FROM Pregunta WHERE idPregunta = '$idPregunta'";
        $this->database->query($sql);
    }

    public function aceptarSugerencia($idPregunta){
        $sql = "UPDATE Pregunta SET esVerificada = 1 WHERE idPregunta = '$idPregunta'";
        $this->database->query($sql);
    }

    public function obtenerPreguntaPorId($idPregunta) {
        $sql = "SELECT P.idPregunta, P.pregunta, P.categoria, P.dificultad, P.fecha_pregunta, P.idUsuario, P.esVerificada,
            R.idRespuesta, R.respuesta, R.esCorrecta
            FROM Pregunta AS P
            LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
            WHERE P.idPregunta = '$idPregunta'";

        $result = $this->database->query($sql);
        $preguntaYRespuestas = [
            'pregunta' => '',
            'respuestas' => [],
        ];

        foreach ($result as $row) {
            if (empty($preguntaYRespuestas['pregunta'])) {
                $preguntaYRespuestas['pregunta'] = $row['pregunta'];
            }

            $preguntaYRespuestas['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'respuesta' => $row['respuesta'],
                'esCorrecta' => $row['esCorrecta'],
            ];
        }

        return $preguntaYRespuestas;
    }

    public function actualizarPregunta($pregunta, $idPregunta, $respuestaCorrecta, $respuestasIncorrectas, $categoria, $dificultad) {
        $sqlUpdatePregunta = "UPDATE Pregunta SET pregunta = '$pregunta', categoria = '$categoria', dificultad = '$dificultad' WHERE idPregunta = '$idPregunta'";
        $this->database->query($sqlUpdatePregunta);

        $sqlUpdateRespuestaCorrecta = "UPDATE Respuesta SET respuesta = '$respuestaCorrecta' WHERE idPregunta = '$idPregunta' AND esCorrecta = 1";
        $this->database->query($sqlUpdateRespuestaCorrecta);

        foreach ($respuestasIncorrectas as $indice => $respuestaIncorrecta) {
            $sqlUpdateRespuestaIncorrecta = "UPDATE Respuesta SET respuesta = '$respuestaIncorrecta' WHERE idPregunta = '$idPregunta' AND esCorrecta = 0 AND idRespuesta = '$indice'";
            $this->database->query($sqlUpdateRespuestaIncorrecta);
        }
    }

    public function reportarPregunta($idPregunta, $idUsuario,  $motivoReporte){
        $sql = "INSERT INTO reporte (idPregunta, idUsuario,  motivoReporte, fechaReporte)
        VALUES ('$idPregunta', '$idUsuario', '$motivoReporte', NOW())";

        $this->database->query($sql);
    }

    public function eliminarReporte($idPregunta){
        $sql = "DELETE FROM reporte WHERE idPregunta = '$idPregunta'";
        $this->database->query($sql);
    }

    public function obtenerCategorias(){
        $query = "SELECT * FROM Categoria";
        $categorias = $this->database->query($query);
        return $categorias;
    }

    public function crearCategoria($nombre, $color, $icono, $idAutor){
        $destino = "public/categorias/";
        $extensionDelArchivo = pathinfo(basename($icono["name"]), PATHINFO_EXTENSION);
        if($extensionDelArchivo != "svg"){
            $_SESSION["alertaCategoria"] = "Por favor, ingresa un archivo svg";
            return false;
        }
        $destinoArchivo = $destino . $nombre . "." . $extensionDelArchivo;

        if(move_uploaded_file($icono["tmp_name"], $destinoArchivo)){
            $query = "INSERT INTO Categoria (nombre, color, fecha, idAutor)
                      VALUES('$nombre', '$color', NOW(), $idAutor)";
            $this->database->query($query); 
        }
        else{
            $_SESSION["alertaCategoria"] = "Ha ocurrido un error al cargar el Icono";
        }
    }

    public function eliminarCategoria($idCategoria){
        $sql = "DELETE FROM Categoria WHERE idCategoria = '$idCategoria'";
        $this->database->query($sql);
    }

    public function buscarCategoriaPorID($idCategoria){
        $sql = "SELECT * FROM Categoria WHERE idCategoria = '$idCategoria'";
        $categoria = $this->database->query($sql);
        return $categoria[0];
    }

    public function editarCategoria($idCategoria, $nombreNuevo, $color, $icono){
        $nombreViejo = $this->database->query("SELECT nombre FROM Categoria WHERE idCategoria = $idCategoria")[0][0];
        Logger::info($nombreViejo);
        $sql = "UPDATE Categoria SET nombre= '$nombreNuevo', color= '$color' WHERE idCategoria = $idCategoria";
        $this->database->query($sql);

        $destino = "public/categorias/";
        $iconoAnterior = $destino . $nombreViejo . ".svg";
        $iconoNuevo = $destino . $nombreNuevo . ".svg";

        if(isset($icono)){
            Logger::info("Icono es distintos de null");
            Logger::info(implode(", ", $icono));
            $extensionDelArchivo = pathinfo(basename($icono["name"]), PATHINFO_EXTENSION);
            if($extensionDelArchivo != "svg"){
                $_SESSION["alertaCategoria"] = "Por favor, ingresa un archivo svg";
                return false;
            }
            
            if(unlink($iconoAnterior)){
                Logger::info("se elimino el icon anterior: " . $iconoAnterior);
                if(!move_uploaded_file($icono["tmp_name"], $iconoNuevo)){
                    $_SESSION["alertaCategoria"] = "Ha ocurrido un error al cargar el Icono";
                }
            }
            else{
                Logger::info("No se elimino el icon anterior: " . $iconoAnterior);
                $_SESSION["alertaCategoria"] = "Ha ocurrido un error al eliminar al reemplazar el Icono";
            }
        }
        else{
            Logger::info("Icono es null");
            if($nombreViejo != $nombreNuevo){
                rename($iconoAnterior, $iconoNuevo);
            }
        }
    }
}