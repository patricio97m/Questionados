<?php
session_start();
include_once ("config/Configuracion.php");

$configuracion = new Configuracion();
$router = $configuracion->getRouter();

$controller = $_GET['controller'] ?? "registro";
$method = $_GET['method'] ?? 'registro';

$router->route($controller, $method);