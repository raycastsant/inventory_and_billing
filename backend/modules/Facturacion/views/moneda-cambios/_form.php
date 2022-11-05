<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\MonedaCambio */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moneda-cambio-form">

    <?php $form = ActiveForm::begin(['options'=>['class'=>'form-inline']]); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'm1_id')->widget(Select2::classname(), [
                    'options' => ['placeholder' => 'Seleccionar ...'],
                    'data' =>  $monedas,
                    'language' => 'es',
                    'pluginOptions' => [
                        'allowClear' => false
                    ], ])->label('1...'); ?>
            <?= $form->field($model, 'valor')->textInput(['type'=>'number', 'step'=>'any'])->label('Equivale a:'); ?>
            <?= $form->field($model, 'm2_id')->widget(Select2::classname(), [
                    'options' => ['placeholder' => 'Seleccionar ...'],
                    'data' =>  $monedas,
                    'language' => 'es',
                    'pluginOptions' => [
                        'allowClear' => false
                    ], ])->label('...'); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
