<?php

/**
 * Script para enviar recordatorios de citas
 * Este script debe ejecutarse cada minuto con un cron job
 * 
 * Ejemplo de cron job (ejecutar cada minuto):
 * * * * * * php /ruta/completa/public/enviar-recordatorios.php
 */

require_once __DIR__ . '/../includes/app.php';

use Classes\Email;
use Model\Cita;

// Obtener la fecha y hora actual
$ahora = new DateTime();

// Calcular la hora dentro de 15 minutos
$en15Minutos = clone $ahora;
$en15Minutos->modify('+15 minutes');

// Formatear para la consulta SQL
$fechaActual = $ahora->format('Y-m-d');
$horaInicio = $ahora->format('H:i:00');
$horaFin = $en15Minutos->format('H:i:59');

// Consulta para obtener las citas que están a 15 minutos
$consulta = "
    SELECT 
        c.id as citaId,
        c.fecha,
        c.hora,
        u.nombre,
        u.apellido,
        u.email,
        s.nombre as servicio_nombre,
        s.precio as servicio_precio
    FROM citas c
    INNER JOIN usuarios u ON c.usuarioId = u.id
    INNER JOIN citasservicios cs ON c.id = cs.citaId
    INNER JOIN servicios s ON cs.servicioId = s.id
    WHERE c.fecha = '{$fechaActual}'
    AND c.hora BETWEEN '{$horaInicio}' AND '{$horaFin}'
    ORDER BY c.id
";

$resultados = Cita::SQL($consulta);

if(empty($resultados)) {
    echo "No hay citas para recordar en este momento.\n";
    exit;
}

// Agrupar por cita
$citasAgrupadas = [];
foreach($resultados as $fila) {
    $citaId = $fila->citaId;
    
    if(!isset($citasAgrupadas[$citaId])) {
        $citasAgrupadas[$citaId] = [
            'email' => $fila->email,
            'nombre' => $fila->nombre . ' ' . $fila->apellido,
            'fecha' => $fila->fecha,
            'hora' => $fila->hora,
            'servicios' => []
        ];
    }
    
    $citasAgrupadas[$citaId]['servicios'][] = [
        'nombre' => $fila->servicio_nombre,
        'precio' => $fila->servicio_precio
    ];
}

// Enviar recordatorios
$enviados = 0;
$errores = 0;

foreach($citasAgrupadas as $citaId => $cita) {
    $email = new Email(
        $cita['email'],
        $cita['nombre'],
        '', // token no necesario
        $cita['fecha'],
        $cita['hora'],
        $cita['servicios']
    );
    
    if($email->enviarRecordatorio()) {
        echo "✓ Recordatorio enviado a {$cita['email']} para cita #{$citaId}\n";
        $enviados++;
    } else {
        echo "✗ Error al enviar recordatorio a {$cita['email']} para cita #{$citaId}\n";
        $errores++;
    }
}

echo "\n=== Resumen ===\n";
echo "Recordatorios enviados: {$enviados}\n";
echo "Errores: {$errores}\n";
echo "Total procesado: " . count($citasAgrupadas) . "\n";
