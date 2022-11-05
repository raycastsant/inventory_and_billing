<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** Para no repetir codigo en el VIEW, Imprimir Oferta e Imprimir Factura*/

$montoProd = 0;
$beneficioProd = 0;
$beneficioTotal = 0;
if(count($model->productosOrdenServicios) > 0) { ?>
    <table class="table table-striped table-bordered">
        <tr><th style="background-color: #ddd;">Producto</th>
            <th style="background-color: #ddd; min-width:100px;">Código</th>
            <th style="background-color: #ddd;">Cant</th>
            <th style="background-color: #ddd;">Precio</th>
        <?php if(!isset($is_pdf)) { ?>
            <th style="background-color: #ddd;">Costo producto</th> 
        <?php if(!isset($is_pdf)) { ?>
            <th style="background-color: #ddd; text-align:right; width:150px;">Beneficio</th>
        <?php } ?>
        <?php } ?>
            <th style="background-color: #ddd; text-align:right; width:150px;">Monto</th>
        </tr>
<?php   
        $costoCalculado;
        $beneficio;
        foreach($model->productosOrdenServicios as $prodOrden) { ?>
        <tr>
            <td><?= $prodOrden->producto->nombre ?> </td>
            <td>
                <?= Html::a($prodOrden->producto->codigo, 
                    Url::toRoute('/inventario/productos/view').'?id='.$prodOrden->producto->id, 
                    ['target' => '_blank']) ?> <?php // echo $prodOrden->producto->codigo ?> 
            </td>
            <td><?= $prodOrden->cant_productos ?></td>
            <td><?= round( ($prodOrden->precio * $cambio), 2).' '.$moneda_salida ?></td>
        <?php if(!isset($is_pdf)) { ?>  
            <td><?= round( ($prodOrden->producto->costo * $cambioCosto), 2).' '.$moneda_salida ?></td>
        <?php } ?>

        <?php 
            $costoCalculado = ($prodOrden->precio * $prodOrden->cant_productos) * $cambio;
            if(!isset($is_pdf)) { ?>
                <td style="text-align:right;"><?php
                        $costo = ($prodOrden->producto->costo * $prodOrden->cant_productos) * $cambioCosto;
                        $beneficio = $costoCalculado - $costo;
                        echo round($beneficio, 2).' '.$moneda_salida; 
                        $beneficioTotal += $beneficio;
                    ?>
                </td>
        <?php } ?>

            <td style="text-align:right;">
                <?php
                    echo round($costoCalculado, 2).' '.$moneda_salida; 
                    $montoProd += $costoCalculado;
                ?>
            </td>
        </tr>
<?php
        } 
        Yii::$app->session->set('beneficioProd', $beneficioTotal);   //subtotal del beneficio
        Yii::$app->session->set('montoProd', $montoProd);   //subtotal del monto
        ?>

        <tr class="info"><th></th><th></th><th></th>
            <?php if(!isset($is_pdf)) { ?>
                    <th></th>
            <?php } ?>
                <th><span class="subtotal">Subtotal</span></th>
            <?php if(!isset($is_pdf)) { ?>
                <th style="text-align:right;"><span class="subtotal"><?= round($beneficioTotal, 2).' '.$moneda_salida ?></span></th>
            <?php } ?>
            <th style="text-align:right;"><span class="subtotal"><?= round($montoProd, 2).' '.$moneda_salida ?></span></th>
        </tr>
    </table>
<?php 
 } 
 ?>