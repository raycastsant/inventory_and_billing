<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\OrdenVentaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-venta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'codigo') ?>

    <?= $form->field($model, 'cliente_id') ?>

    <?= $form->field($model, 'estado_orden_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'fecha_iniciada') ?>

    <?php // echo $form->field($model, 'fecha_cerrada') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reiniciar', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
