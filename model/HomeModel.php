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
}