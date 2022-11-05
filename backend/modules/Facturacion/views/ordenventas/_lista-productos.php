<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** Para no repetir codigo en el VIEW, Imprimir Oferta e Imprimir Factura*/

$montoProd = 0;
$beneficioTotal = 0;
if(count($model->productosOrdenVentas) > 0) {
    $madicional_colspan = 4;    
?>
    <table class="table table-striped table-bordered">
        <tr><th style="background-color: #ddd;">Producto</th>
            <th style="background-color: #ddd; min-width:100px;">Código</th>
            <th style="background-color: #ddd;">Cant</th>
            <th style="background-color: #ddd;">Precio</th>
        <?php if(!isset($is_pdf)) { 
                $madicional_colspan++;
            ?>
                <th style="background-color: #ddd;">Costo producto</th> 
        <?php } ?>

            <th style="background-color: #ddd; text-align:right;">Monto</th>

        <?php if(!isset($is_pdf)) { 
           //     $madicional_colspan++; 
            ?>
                <th style="background-color: #ddd; text-align:right;">Beneficio</th>
        <?php } ?>
        </tr>
<?php   
        $costoCalculado;
        $beneficio;
        foreach($model->productosOrdenVentas as $prodOrden) { ?>
        <tr>
            <td><?= $prodOrden->producto->nombre ?> </td>
            <td> <?= Html::a($prodOrden->producto->codigo, 
                    Url::toRoute('/inventario/productos/view').'?id='.$prodOrden->producto->id, 
                    ['target' => '_blank']) ?> <?php // echo $prodOrden->producto->codigo ?> </td>
            <td><?= $prodOrden->cantidad ?></td>
            <td><?= round( ($prodOrden->precio * $cambio), 2).' '.$moneda_salida ?></td>
        <?php if(!isset($is_pdf)) { ?>  
            <td><?= round( ($prodOrden->producto->costo * $cambioCosto), 2).' '.$moneda_salida ?></td>
        <?php } ?>
            <td style="text-align:right;"><?php
                    $costoCalculado = ($prodOrden->precio * $prodOrden->cantidad) * $cambio;
                    echo round($costoCalculado, 2).' '.$moneda_salida; 
                    $montoProd += $costoCalculado;
                ?></td>
        <?php if(!isset($is_pdf)) { ?>
            <td style="text-align:right;"><?php
                    $costo = ($prodOrden->producto->costo * $prodOrden->cantidad) * $cambioCosto;
                    $beneficio = $costoCalculado - $costo;
                    echo round($beneficio, 2).' '.$moneda_salida; 
                    $beneficioTotal += $beneficio;
                ?></td>
        <?php } ?>
        </tr>
<?php
        }

        //Monto adicional
        if($model->monto_adicional > 0) { 
            $MA = ($model->monto_adicional * $cambio);
            $montoProd += $MA;
        ?>
            <tr>
                <td colspan="<?= $madicional_colspan ?>" style="text-align:right;"><?= $model->monto_adicional_desc ?></td>
                <td style="text-align:right;"><?= $MA.' '.$moneda_salida ?></td>

                <?php if(!isset($is_pdf)) { 
                        $beneficioTotal += $MA;
                ?>
                    <td style="text-align:right;"><?= $MA.' '.$moneda_salida ?></td>   <!-- Beneficio -->
                <?php } ?>
            </tr>
<?php
        }
?>

        <tr class="info"><th></th><th></th><th></th>
            <?php if(!isset($is_pdf)) { ?>
                    <th></th>
            <?php } ?>
                <th style="text-align:right;"><span class="subtotal">Total</span></th>
                <th style="text-align:right;"><span class="subtotal"><?= round($montoProd, 2).' '.$moneda_salida ?></span></th>
            <?php if(!isset($is_pdf)) { ?>
                <th style="text-align:right;"><span class="subtotal"><?= round($beneficioTotal, 2).' '.$moneda_salida ?></span></th>
            <?php } ?>
        </tr>
    </table>
<?php 
 } 
 ?>