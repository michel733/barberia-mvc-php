<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar() {
        
        // Antes de guardar, verificar que el horario no esté reservado (±40 minutos)
        $fecha = $_POST['fecha'] ?? null;
        $hora = $_POST['hora'] ?? null;

        if($fecha && $hora) {
            $consulta = "SELECT hora FROM citas WHERE fecha = '" . $fecha . "'";
            $citasExistentes = Cita::SQL($consulta);

            $timestampSolicitado = strtotime($fecha . ' ' . $hora);
            foreach($citasExistentes as $ce) {
                $timestampExistente = strtotime($fecha . ' ' . $ce->hora);
                $diff = abs($timestampSolicitado - $timestampExistente);
                if($diff <= 40 * 60) {
                    // Devuelve error en JSON para que el front pueda manejarlo
                    echo json_encode(['resultado' => false, 'error' => 'Horario reservado']);
                    return;
                }
            }
        }

        // Almacena la Cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        // Almacena la Cita y el Servicio

        // Almacena los Servicios con el ID de la Cita
        $idServicios = explode(",", $_POST['servicios']);
        foreach($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar() {
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }

    // Verifica si un horario está reservado en un rango de +-40 minutos para la fecha dada
    public static function verificar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha = $_POST['fecha'] ?? null;
            $hora = $_POST['hora'] ?? null;

            if(!$fecha || !$hora) {
                echo json_encode(['reservado' => false, 'error' => 'Faltan parámetros']);
                return;
            }

            // Obtener todas las horas de citas para la fecha
            $consulta = "SELECT hora FROM citas WHERE fecha = '" . $fecha . "'";
            $citas = Cita::SQL($consulta);

            $reservado = false;
            $conflictos = [];

            $timestampSolicitado = strtotime($fecha . ' ' . $hora);

            foreach($citas as $c) {
                $horaExistente = $c->hora;
                $timestampExistente = strtotime($fecha . ' ' . $horaExistente);

                // Diferencia en segundos
                $diff = abs($timestampSolicitado - $timestampExistente);

                if($diff <= 40 * 60) { // 40 minutos en segundos
                    $reservado = true;
                    $conflictos[] = $horaExistente;
                }
            }

            echo json_encode(['reservado' => $reservado, 'conflictos' => $conflictos]);
        }
    }
}