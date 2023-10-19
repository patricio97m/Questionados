<?php

class JuegoModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPreguntaAlAzar()
    {
        // Obtén las preguntas utilizadas en la sesión
        $preguntasUtilizadas = $_SESSION['preguntas_utilizadas'] ?? [];
        $totalPreguntas = $this->obtenerCantidadTotalDePreguntas();

        // Verificar si todas las preguntas se han utilizado
        if (count($preguntasUtilizadas) >= $totalPreguntas) {
            // Reiniciar el registro de preguntas utilizadas
            $preguntasUtilizadas = [];
        }

        $baseQuery = "SELECT idPregunta, pregunta, categoria FROM Pregunta";

        if (!empty($preguntasUtilizadas)) {
            // Si hay preguntas utilizadas, sacarlas de la consulta
            $baseQuery .= " WHERE idPregunta NOT IN (" . implode(',', $preguntasUtilizadas) . ")";
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
                'categoria' => $pregunta[0]['categoria'],
                'respuestas' => $respuestas
            ];
        }

        return null;
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
}