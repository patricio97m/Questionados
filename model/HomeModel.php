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

    public function obtenerCantidadJugadoresPorFecha($periodo) {
        $orderByClause = 'DESC';

        if ($periodo === 'mes') {
            $periodoClausula = 'WHERE DATE(fecha_alta) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($periodo === 'semana') {
            $periodoClausula = 'WHERE DATE(fecha_alta) >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
        } elseif ($periodo === 'dia') {
            $periodoClausula = 'WHERE DATE(fecha_alta) >= CURDATE()';
        }
        elseif ($periodo === 'historico') {
            $periodoClausula = '';
        }else $periodoClausula = 'WHERE DATE(fecha_alta) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';

        $query = "select count(cantidad_jugadores) as cantidad_jugadores,'$periodo' as periodo from ( SELECT count(fecha_alta) AS cantidad_jugadores 
                FROM Usuario $periodoClausula GROUP BY usuario ) as a";

        return $this->database->query($query);
    }

    public function obtenerCantidadPartidasPorFecha($periodo) {

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

        $query = "select 
                    count(cantidad_partidas) as cantidad_partidas ,'$periodo' as periodo
                    from 
                    ( 
                        SELECT count(id) AS cantidad_partidas 
                        FROM partida 
                        $periodoClausula
                        GROUP BY id ) as a";

        return $this->database->query($query);
    }

    public function obtenerCantidadPreguntasPorFecha($periodo) {
        $periodoClausula = '';

        if ($periodo === 'mes') {
            $periodoClausula = 'WHERE DATE(fecha_pregunta) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        } elseif ($periodo === 'semana') {
            $periodoClausula = 'WHERE DATE(fecha_pregunta) >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
        } elseif ($periodo === 'dia') {
            $periodoClausula = 'WHERE DATE(fecha_pregunta) >= CURDATE()';
        }
        elseif ($periodo === 'historico') {
            $periodoClausula = '';
        }else $periodoClausula = 'WHERE DATE(fecha_pregunta) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';

        $query = "SELECT 
                COUNT(idPregunta) AS cantidad_preguntas,
                '$periodo' AS periodo
              FROM Pregunta 
              $periodoClausula";

        return $this->database->query($query);
    }

    public function obtenerUsuariosPorSexo(){
        $query = "SELECT sexo, COUNT(*) as cantidad_usuarios FROM Usuario GROUP BY sexo";
        return $this->database->query($query);
    }
    public function obtenerUsuariosPorEdad() {
        $query = "SELECT
                SUM(CASE WHEN DATEDIFF(CURDATE(), fecha_nac) < 18 * 365 THEN 1 ELSE 0 END) AS Menores,
                SUM(CASE WHEN DATEDIFF(CURDATE(), fecha_nac) >= 18 * 365 AND DATEDIFF(CURDATE(), fecha_nac) < 60 * 365 THEN 1 ELSE 0 END) AS Medios,
                SUM(CASE WHEN DATEDIFF(CURDATE(), fecha_nac) >= 60 * 365 THEN 1 ELSE 0 END) AS Jubilados
              FROM Usuario";

        return $this->database->query($query);
    }
    public function obtenerUsuariosPorPorcentajeDePreguntas() {
        $query = "
        SELECT
    u.idUsuario,
    u.usuario, COUNT(ru.idRespuestaUsuario) AS total_respuestas, SUM(ru.esCorrecta) AS respuestas_correctas, IFNULL(SUM(ru.esCorrecta) / COUNT(ru.idRespuestaUsuario) * 100, 0) AS porcentaje_correctas
    FROM Usuario u
    LEFT JOIN RespuestasUsuario ru ON u.idUsuario = ru.idUsuario
    WHERE ru.idRespuestaUsuario IS NOT NULL
    GROUP BY u.idUsuario, u.usuario
    ORDER BY porcentaje_correctas DESC;
    ";

        return $this->database->query($query);
    }

    public function obtenerUsuariosPorPais() {
        $query = "SELECT pais, COUNT(idUsuario) as cantidad_usuarios FROM Usuario GROUP BY pais ORDER BY cantidad_usuarios DESC";
        return $this->database->query($query);
    }

    public function obtenerPreguntasAModerar() {
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario, C.nombre as categoria
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        LEFT JOIN usuario AS U ON P.idUsuario = U.idUsuario
        LEFT JOIN Categoria AS C ON C.idCategoria = P.idCategoria
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
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario, REP.motivoReporte, REP.fechaReporte, C.nombre
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        INNER JOIN Reporte AS REP ON P.idPregunta = REP.idPregunta
        LEFT JOIN Usuario AS U ON REP.idUsuario = U.idUsuario
        LEFT JOIN Categoria AS C ON P.idCategoria = C.idCategoria
        ORDER BY REP.fechaReporte";

        $preguntasConRespuestas = $this->database->query($query);
        Logger::info(json_encode($preguntasConRespuestas));

        $preguntas = [];

        foreach ($preguntasConRespuestas as $row) {
            $idPregunta = $row['idPregunta'];

            if (!isset($preguntas[$idPregunta])) {
                $preguntas[$idPregunta] = [
                    'idPregunta' => $row['idPregunta'],
                    'pregunta' => $row['pregunta'],
                    'fecha_pregunta' => $row['fechaReporte'],
                    'categoria' => $row['nombre'],
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
        $query = "SELECT P.*, R.idRespuesta, R.respuesta, R.esCorrecta, U.usuario, C.nombre as categoria
        FROM Pregunta AS P
        LEFT JOIN Respuesta AS R ON P.idPregunta = R.idPregunta
        LEFT JOIN usuario AS U ON P.idUsuario = U.idUsuario
        LEFT JOIN Categoria AS C ON C.idCategoria = P.idCategoria
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

    public function obtenerCategorias(){
        $query = "SELECT C.*, U.usuario AS autor 
                  FROM Categoria AS C
                  LEFT JOIN usuario AS U ON C.idAutor = U.idUsuario";
        $categorias = $this->database->query($query);
        return $categorias;
    }
}