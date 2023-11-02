<?php

class HomeModel
{
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function obtenerPartidasPorId($idUsuario) {
        return $this->database->query("SELECT * FROM `partida` WHERE idusuario = '$idUsuario' ORDER BY fecha_partida DESC LIMIT 10");
    }
    public function obtenerPuntajeTotalPorId($idUsuario) {
        $query = "SELECT SUM(puntaje_obtenido) AS puntaje_total FROM partida WHERE idusuario = '$idUsuario'";
        $resultado = $this->database->query($query);

        return (int) $resultado[0]['puntaje_total'];
    }

    public function obtenerRankingUsuarios() {
        $query = "SELECT u.usuario, SUM(p.puntaje_obtenido) AS puntaje_total
              FROM Usuario u
              INNER JOIN Partida p ON u.idUsuario = p.idUsuario
              GROUP BY u.usuario
              ORDER BY puntaje_total DESC
              LIMIT 5";

        return $this->database->query($query);
    }

    public function obtenerMejoresPuntajesPorFecha($periodo) {
        $orderByClause = 'DESC';

        if ($periodo === 'mes') {
            $periodoClausula = 'WHERE DATE(fecha_partida) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($periodo === 'semana') {
            $periodoClausula = 'WHERE DATE(fecha_partida) >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
        } elseif ($periodo === 'dia') {
            $periodoClausula = 'WHERE DATE(fecha_partida) >= CURDATE()';
        }
        elseif ($periodo === 'historico') {
            $periodoClausula = '';
        }else $periodoClausula = 'WHERE DATE(fecha_partida) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';

        $query = "SELECT u.usuario, SUM(p.puntaje_obtenido) AS puntaje_total
              FROM Usuario u
              INNER JOIN Partida p ON u.idUsuario = p.idUsuario
              $periodoClausula
              GROUP BY u.usuario
              ORDER BY puntaje_total $orderByClause
              LIMIT 15";

        return $this->database->query($query);
    }

    public function obtenerPreguntasAModerar() {
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        LEFT JOIN usuario AS U ON P.idUsuario = U.idUsuario
        WHERE P.esVerificada = false
        ORDER BY P.fecha_pregunta";

        $preguntasConRespuestas = $this->database->query($query);

        $preguntas = [];

        foreach ($preguntasConRespuestas as $row) {
            $idPregunta = $row['idPregunta'];

            if (!isset($preguntas[$idPregunta])) {
                $preguntas[$idPregunta] = [
                    'idPregunta' => $row['idPregunta'],
                    'pregunta' => $row['pregunta'],
                    'fecha_pregunta' => $row['fecha_pregunta'],
                    'categoria' => $row['categoria'],
                    'dificultad' => $row['dificultad'],
                    'usuario' => $row['usuario'],
                    'respuestas' => [],
                ];
            }

            $preguntas[$idPregunta]['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'respuesta' => $row['respuesta'],
                'esCorrecta' => $row['esCorrecta'],
            ];
        }

        return array_values($preguntas);

    }

    public function obtenerReportes() {
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario, REP.motivoReporte
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        INNER JOIN Reporte AS REP ON P.idPregunta = REP.idPregunta
        LEFT JOIN Usuario AS U ON REP.idUsuario = U.idUsuario
        ORDER BY P.fecha_pregunta";

        $preguntasConRespuestas = $this->database->query($query);
        Logger::info(json_encode($preguntasConRespuestas));

        $preguntas = [];

        foreach ($preguntasConRespuestas as $row) {
            $idPregunta = $row['idPregunta'];

            if (!isset($preguntas[$idPregunta])) {
                $preguntas[$idPregunta] = [
                    'idPregunta' => $row['idPregunta'],
                    'pregunta' => $row['pregunta'],
                    'fecha_pregunta' => $row['fecha_pregunta'],
                    'categoria' => $row['categoria'],
                    'dificultad' => $row['dificultad'],
                    'usuario' => $row['usuario'],
                    'motivoReporte' => $row['motivoReporte'],
                    'respuestas' => [],
                ];
            }

            $preguntas[$idPregunta]['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'respuesta' => $row['respuesta'],
                'esCorrecta' => $row['esCorrecta'],
            ];
        }

        return array_values($preguntas);
    }

    public function obtenerPreguntasVerificadas() {
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        LEFT JOIN usuario AS U ON P.idUsuario = U.idUsuario
        WHERE P.esVerificada = true
        ORDER BY P.fecha_pregunta DESC";

        $preguntasConRespuestas = $this->database->query($query);

        $preguntas = [];

        foreach ($preguntasConRespuestas as $row) {
            $idPregunta = $row['idPregunta'];

            if (!isset($preguntas[$idPregunta])) {
                $preguntas[$idPregunta] = [
                    'idPregunta' => $row['idPregunta'],
                    'pregunta' => $row['pregunta'],
                    'fecha_pregunta' => $row['fecha_pregunta'],
                    'categoria' => $row['categoria'],
                    'dificultad' => $row['dificultad'],
                    'usuario' => $row['usuario'],
                    'respuestas' => [],
                ];
            }

            $preguntas[$idPregunta]['respuestas'][] = [
                'idRespuesta' => $row['idRespuesta'],
                'respuesta' => $row['respuesta'],
                'esCorrecta' => $row['esCorrecta'],
            ];
        }

        return array_values($preguntas);
    }
}