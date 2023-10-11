<?php

class JuegoModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function obtenerPreguntaAlAzar() {
        // Se consulta la pregunta al azar
        $query = "SELECT idPregunta, pregunta, categoria FROM Pregunta ORDER BY RAND() LIMIT 1";
        $pregunta = $this->database->query($query);

        if ($pregunta) {
            // Se obtienen las respuestas de esa pregunta al azar
            $respuestasQuery = "SELECT idRespuesta, respuesta, esCorrecta FROM Respuesta WHERE idPregunta = " . $pregunta[0]['idPregunta'] . " ORDER BY RAND()";
            $respuestas = $this->database->query($respuestasQuery);

            // Se combina la pregunta con sus respuestas
            return [
                'pregunta' => $pregunta[0]['pregunta'],
                'categoria' => $pregunta[0]['categoria'],
                'respuestas' => $respuestas
            ];
        }
        return null;
    }
}