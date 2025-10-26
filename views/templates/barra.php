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
<?php } ?>