<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>

<?= $this->render('_print-header', ['pname' => 'OFERTA', 'ceoInfo' => $ceoInfo, 'model' => $model]); ?>

<?php
    /**Lista de productos */ 
        echo $this->render('_lista-productos', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'is_pdf' => true]);
   ?>
