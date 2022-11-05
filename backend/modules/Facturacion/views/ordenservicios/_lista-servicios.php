<?php
/** Para no repetir codigo en el VIEW, Imprimir Oferta e Imprimir Factura*/

$montoServ = 0;
if(count($model->getServicios()) > 0) {  ?>
    <table class="table table-striped table-bordered">
        <tr>
            <th style="background-color: #ddd;">Servicios prestados</th>
        <?php if(!isset($is_pdf)) { ?>
            <th style="background-color: #ddd; text-align:right; width:150px;">Beneficio</th>
        <?php } ?>
            <th style="background-color: #ddd; text-align:right; width:150px;">Costo</th>
        </tr>
<?php   
    foreach($model->getServicios() as $servOrden) {
        $precio = ($servOrden['precio'] * $cambio); 
?>
        <tr>
            <td><?php echo $servOrden['nombre'] ?> </td>
        <?php if(!isset($is_pdf)) { ?>
            <td style="text-align:right; width:150px;"><?php echo round($precio, 2).' '.$moneda_salida ?></td> <!-- beneficio -->
        <?php } ?>
            <td style="text-align:right; width:150px;"><?php echo round($precio, 2).' '.$moneda_salida ?></td> <!-- Monto -->
            <?php $montoServ += $precio; ?>
        </tr>
<?php  } ?>

        <tr class="info">
            <th><span class="subtotal">Subtotal</span></th>
        <?php if(!isset($is_pdf)) { ?>
            <th style="text-align:right;"><span class="subtotal"><?= round($montoServ, 2).' '.$moneda_salida ?></span></th><!-- beneficio -->
        <?php } ?>
            <th style="text-align:right;"><span class="subtotal"><?= round($montoServ, 2).' '.$moneda_salida ?></span></th> <!-- Monto -->
        </tr>

    </table>
<?php 
    Yii::$app->session->set('beneficioServ', $montoServ);  //Por el momento es igual al monto
    Yii::$app->session->set('montoServ', $montoServ);
}  ?>