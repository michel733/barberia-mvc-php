<?php
// Asegurar que la sesión esté iniciada para leer $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<div class="barra">
    <p>Hola: <?php echo $nombre ?? ''; ?></p>
    <a class="boton" href="/logout">Cerrar Sesión</a>
</div>

<?php if(!empty($_SESSION['admin']) && $_SESSION['admin'] == 1) { ?>
    <div class="barra-servicios">
        <a class="boton" href="/admin">Ver Citas</a>
        <a class="boton" href="/servicios">Ver Servicios</a>
        <a class="boton" href="/servicios/crear">Nuevo Servicio</a>
    </div>
<?php } else { ?>
    <div class="barra-servicios">
        <?php 
        $currentUrl = $_SERVER['REQUEST_URI'];
        if(strpos($currentUrl, '/mis-citas') !== false) { ?>
            <a class="boton" href="/cita">Nueva Cita</a>
        <?php } else { ?>
            <a class="boton" href="/mis-citas">Mis Citas</a>
        <?php } ?>
    </div>
<?php } ?>