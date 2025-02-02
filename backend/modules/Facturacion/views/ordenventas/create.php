<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\UserRole;
use kartik\select2\Select2;


$this->title = 'Crear orden de ventas';
$this->params['breadcrumbs'][] = ['label' => 'Orden Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-venta-create">
    <legend><?= Html::encode($this->title) ?></legend>
    <div class="orden-venta-form">
        <?php
        $form = ActiveForm::begin(['enableClientValidation' => false]);
        echo Html::submitButton('Guardar', ['class' => 'btn btn-success pull-right']);
        ?>

        <?= $model->errorSummary($form); ?>

        <div class="well">
            <fieldset>
                <div class="row">
                    <div class="col-md-3">
                        <?php
                        $date = new DateTime();
                        echo $form->field($model->ordenVenta, 'codigo')->textInput(['maxlength' => true, 'value' => $date->format('Y') . '/' . sprintf("%06d", $serie), 'readonly' => 'readonly'])->label('Código') . '&nbsp;&nbsp;';
                        echo $form->field($model->ordenVenta, 'serie')->hiddenInput(['value' => $serie])->label(false);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                        echo $form->field($model->ordenVenta, 'cliente_id')->widget(Select2::class, [
                            'data' => $clientes,
                            'language' => 'es',
                            'options' => [
                                'placeholder' => '-Seleccionar Cliente-',
                            ],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ])->label('Cliente') . '&nbsp;&nbsp;';  ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                        $user_rol = Yii::$app->session->get('user_rol');
                        if ($user_rol == UserRole::ROL_GESTOR_AREA)
                            echo $form->field($model->ordenVenta, 'area_id')->hiddenInput(['value' => $area_id])->label(false);
                        else
                            echo $form->field($model->ordenVenta, 'area_id')->dropDownList($areas);
                        ?>
                    </div>
                </div>
                <?php
                echo $form->field($model->ordenVenta, 'estado_orden_id')->hiddenInput(['value' => $estado_id])->label(false);
                echo $form->field($model->ordenVenta, 'fecha_iniciada')->hiddenInput(['value' => date("Y-m-d")])->label(false);
                ?>
            </fieldset>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>