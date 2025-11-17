<?php

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index( Router $router ) {

        session_start();

        isAuth();

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'id' => $_SESSION['id'] ?? ''
        ]);
    }

    public static function misCitas( Router $router ) {

        session_start();

        isAuth();

        $id = $_SESSION['id'];

        // Consultar las citas del usuario
        $consulta = "SELECT citas.id, citas.fecha, citas.hora, ";
        $consulta .= "servicios.nombre as servicio, servicios.precio ";
        $consulta .= "FROM citas ";
        $consulta .= "LEFT OUTER JOIN citasservicios ON citasservicios.citaId = citas.id ";
        $consulta .= "LEFT OUTER JOIN servicios ON servicios.id = citasservicios.servicioId ";
        $consulta .= "WHERE citas.usuarioId = '{$id}' ";
        $consulta .= "ORDER BY citas.fecha DESC, citas.hora DESC";

        $citas = \Model\AdminCita::SQL($consulta);

        $router->render('cita/mis-citas', [
            'nombre' => $_SESSION['nombre'] ?? '',
            'citas' => $citas
        ]);
    }
}