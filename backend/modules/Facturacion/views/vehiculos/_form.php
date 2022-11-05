<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Vehiculo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vehiculo-form">

    <?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'cliente_id')->widget(Select2::classname(), [
                        'data' =>  $clientes,
                        'language' => 'es',
                        'pluginOptions' => [
                            'allowClear' => false
                        ], ])->label('Cliente'); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'marca')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4"></div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'chapa')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'modelo')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'fabricante')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'anno')->widget(Select2::classname(), [
                        'data' =>  $annos,
                        'options' => ['placeholder' => '-Seleccionar-'],
                        'language' => 'es',
                        'pluginOptions' => [
                            'allowClear' => false
                        ], ])->label('Año'); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'codigo_motor')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'codigo_alternador')->textInput(['maxlength' => true]) ?>
    </div>
</div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
