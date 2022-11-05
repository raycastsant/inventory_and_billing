<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Moneda */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moneda-form">

    <?php $form = ActiveForm::begin(['options'=>['class'=>'form-inline']]); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
