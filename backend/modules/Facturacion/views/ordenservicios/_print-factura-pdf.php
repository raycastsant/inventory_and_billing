<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>

<?= $this->render('_print-header', ['pname' => 'FACTURA', 'ceoInfo' => $ceoInfo, 'model' => $model]); ?>
<!--Header End -->

<!--<table>
    <tr>
        <td style="min-width: 800px;"><b>Información de facturación:</b></td> 
        <td><b><?php if(isset($model->vehiculo)) echo "Información del vehículo: "; ?></b></td> 
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;Nombre: <?= $model->vehiculo->cliente->nombre; ?></td>
        <td>&nbsp;&nbsp;&nbsp; <?php if(isset($model->vehiculo)) echo "Chapa: ".$model->vehiculo->chapa; ?></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;Teléfono: <?= $model->vehiculo->cliente->telefono; ?></td>
        <td>&nbsp;&nbsp;&nbsp; <?php if(isset($model->vehiculo)) echo "Marca: ".$model->vehiculo->marca; ?></td>
    </tr>
    <tr>
        <td></td>
        <td>&nbsp;&nbsp;&nbsp; <?php if(isset($model->vehiculo)) echo "Modelo: ".$model->vehiculo->modelo; ?></td>
    </tr>
</table>-->

<?php
    Yii::$app->session->set('montoProd', 0);
    Yii::$app->session->set('montoServ', 0);

    /**Lista de productos */ 
    echo $this->render('_lista-productos', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'is_pdf' => true]);
    $montoProd =  Yii::$app->session->get('montoProd');

    /**Lista de servicios */ 
    echo $this->render('_lista-servicios', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'is_pdf' => true]);
    $montoServ =  Yii::$app->session->get('montoServ');
?>

    <table class="table table-striped table-bordered">
        <tr>
            <th style="background-color: #ddd; text-align:right;"><span class="subtotal">COSTO TOTAL</span></th>
            <th style="background-color: #ddd; text-align:right;"><span class="subtotal"><?= round(($montoServ+$montoProd), 2 ).' '.$moneda_salida ?></span></th>
        </tr>
    </table>
