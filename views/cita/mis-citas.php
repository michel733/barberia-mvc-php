<h1 class="nombre-pagina">Mis Citas</h1>
<p class="descripcion-pagina">Aquí puedes ver todas tus citas programadas</p>

<?php 
    include_once __DIR__ . '/../templates/barra.php';
?>

<div id="citas-usuario">
    <?php if(empty($citas)) { ?>
        <div class="alerta exito" style="text-align: center; margin: 3rem 0;">
            <p>No tienes citas programadas</p>
            <a href="/cita" class="boton" style="display: inline-block; margin-top: 2rem;">Crear Nueva Cita</a>
        </div>
    <?php } else { ?>
        <ul class="citas">
            <?php 
                $idCita = 0;
                foreach($citas as $key => $cita) {
                    if($idCita !== $cita->id) {
                        $total = 0;
            ?>
            <li>
                <p>ID: <span><?php echo $cita->id; ?></span></p>
                <p>Fecha: <span><?php echo $cita->fecha ? date('d/m/Y', strtotime($cita->fecha)) : 'Sin fecha'; ?></span></p>
                <p>Hora: <span><?php echo $cita->hora ?? 'Sin hora'; ?></span></p>
                
                <h3>Servicios</h3>
            <?php 
                $idCita = $cita->id;
            }
                $total += $cita->precio;
            ?>
                <p class="servicio"><?php echo ($cita->servicio ?? 'Sin servicio') . " - $" . ($cita->precio ?? '0.00'); ?></p>
            
            <?php 
                $actual = $cita->id;
                $proximo = $citas[$key + 1]->id ?? 0;
                
                if(esUltimo($actual, $proximo)) { 
            ?>
                <p class="total">Total: <span>$<?php echo number_format($total, 2); ?></span></p>
                
                <div class="acciones" style="margin-top: 2rem;">
                    <form action="/api/eliminar" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta cita?');">
                        <input type="hidden" name="id" value="<?php echo $cita->id; ?>">
                        <input type="submit" class="boton-eliminar" value="Cancelar Cita">
                    </form>
                </div>
            </li>
            <?php 
                }
            } 
            ?>
        </ul>
    <?php } ?>
</div>

<?php
    $script = "<script src='build/js/app.js'></script>"
?>
