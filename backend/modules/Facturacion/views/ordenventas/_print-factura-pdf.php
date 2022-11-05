<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
    <?php /*echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'captionOptions' => ['style' => 'width:200px'],
                'attribute'=>'codigo',
                'label'=>'Código',
            ],
            [
                'attribute'=>'cliente',
                'label'=>'Cliente',
                'value'=>$model->cliente->nombre
            ],
            [
                'attribute'=>'fecha_iniciada',
                'label'=>'Fecha',
                'value'=>date('d/m/Y')
            ],
        ],
    ]);*/
?>

<?= $this->render('_print-header', ['pname' => 'FACTURA', 'ceoInfo' => $ceoInfo, 'model' => $model]); ?>
<!--Header End -->

<?php
    /**Lista de productos */ 
        echo $this->render('_lista-productos', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'is_pdf' => true]);
   ?>
