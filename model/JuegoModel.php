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
        // Obtén las preguntas utilizadas en la sesión
        $preguntasUtilizadas = $_SESSION['preguntas_utilizadas'] ?? [];
        $dificultadUsuario = $this->calcularDificultadUsuario($idUsuario);
        Logger::info("Dificultad del usuario: " . $dificultadUsuario);

        // Verificar si la dificultad del usuario existe en la tabla Pregunta
        $verificarDificultadQuery = "SELECT COUNT(*) as existe_dificultad FROM Pregunta WHERE dificultad = '$dificultadUsuario' AND esVerificada = true";
        $result = $this->database->query($verificarDificultadQuery);

        if ($result && $result[0]['existe_dificultad'] > 0) {
            // Si la dificultad del usuario existe, intenta obtener una pregunta de esa dificultad
            $baseQuery = "SELECT idPregunta, pregunta, categoria, dificultad FROM Pregunta WHERE dificultad = '$dificultadUsuario' AND esVerificada = true";
            Logger::info("pregunta de la misma dificultad");

            if (!empty($preguntasUtilizadas)) {
                // Si hay preguntas utilizadas, exclúyelas de la consulta
                $baseQuery .= " AND idPregunta NOT IN (" . implode(',', $preguntasUtilizadas) . ")";
            }

            $baseQuery .= " ORDER BY RAND() LIMIT 1";
            $pregunta = $this->database->query($baseQuery);

            if ($pregunta) {
                $preguntasUtilizadas[] = $pregunta[0]['idPregunta'];
                $_SESSION['preguntas_utilizadas'] = $preguntasUtilizadas;

                // Obtener las respuestas de esa pregunta al azar
                $respuestasQuery = "SELECT idRespuesta, respuesta, esCorrecta FROM Respuesta WHERE idPregunta = " . $pregunta[0]['idPregunta'] . " ORDER BY RAND()";
                $respuestas = $this->database->query($respuestasQuery);

                // Combinar la pregunta con sus respuestas
                return [
                    'pregunta' => $pregunta[0]['pregunta'],
                    'idPregunta' => $pregunta[0]['idPregunta'],
                    'categoria' => $pregunta[0]['categoria'],
                    'dificultad' => $pregunta[0]['dificultad'],
                    'respuestas' => $respuestas
                ];
            }
        }

        // Si no se encuentra una pregunta de la misma dificultad, busca cualquier pregunta no utilizada sin restricciones de dificultad
        $baseQuery = "SELECT idPregunta, pregunta, categoria, dificultad FROM Pregunta WHERE esVerificada = true";
        if (!empty($preguntasUtilizadas)) {
            // Si hay preguntas utilizadas, se excluyen de la consulta
            $baseQuery .= " AND idPregunta NOT IN (" . implode(',', $preguntasUtilizadas) . ")";
        }
        $baseQuery .= " ORDER BY RAND() LIMIT 1";
        $pregunta = $this->database->query($baseQuery);
        Logger::info("pregunta de dificultad diferente.");

        if ($pregunta) {
            $preguntasUtilizadas[] = $pregunta[0]['idPregunta'];
            $_SESSION['preguntas_utilizadas'] = $preguntasUtilizadas;

            // Obtener las respuestas de esa pregunta al azar
            $respuestasQuery = "SELECT idRespuesta, respuesta, esCorrecta FROM Respuesta WHERE idPregunta = " . $pregunta[0]['idPregunta'] . " ORDER BY RAND()";
            $respuestas = $this->database->query($respuestasQuery);

            // Combinar la pregunta con sus respuestas
            return [
                'pregunta' => $pregunta[0]['pregunta'],
                'idPregunta' => $pregunta[0]['idPregunta'],
                'categoria' => $pregunta[0]['categoria'],
                'dificultad' => $pregunta[0]['dificultad'],
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
        $sql = "INSERT INTO Pregunta (pregunta, categoria, dificultad, fecha_pregunta, idUsuario, esVerificada) 
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
}