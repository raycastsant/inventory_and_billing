<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\ProductoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="producto-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tipoproducto_id') ?>

    <?= $form->field($model, 'unidad_medida_id') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'costo') ?>

    <?php // echo $form->field($model, 'precio') ?>

    <?php // echo $form->field($model, 'codigo') ?>

    <?php // echo $form->field($model, 'desc') ?>

    <?php // echo $form->field($model, 'existencia') ?>

    <?php // echo $form->field($model, 'fab_codigo') ?>

    <?php // echo $form->field($model, 'fab_medida') ?>

    <?php // echo $form->field($model, 'fab_peso') ?>

    <?php // echo $form->field($model, 'nombre_imagen') ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reiniciar', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
