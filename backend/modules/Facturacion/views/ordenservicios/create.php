<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\UserRole;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\OrdenServicio */

$this->title = 'Crear orden de servicios';
$this->params['breadcrumbs'][] = ['label' => 'Listar órdenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-servicio-create">

    <legend><?= Html::encode('Orden de Servicios') ?></legend>

    <?php /* echo $this->render('_formcreate', [
        'model' => $model,  'clientes' => $clientes, 'estado_id'=>$estado_id, 
        'user_id'=>$user_id, 'servicios' => $servicios, 'trabajadores' => $trabajadores, 'isCreate' => true, 
        'serie' => $serie
    ])*/ ?>

    <div class="orden-servicio-form">
        <?php
            $form = ActiveForm::begin(['enableClientValidation' => false]); 
        ?>
        
        <?= $model->errorSummary($form); ?>
        <div class="well">
            <fieldset>
                <div class="row">
                    <div class="col-md-3">
                        <?php 
                            $date = new DateTime();
                            echo $form->field($model->ordenServicio, 'codigo')->textInput(['maxlength' => true, 'value'=>$date->format('Y').'/'.sprintf("%06d", $serie), 'readonly'=>'readonly'])->label('Código').'&nbsp;&nbsp;'; 
                            echo $form->field($model->ordenServicio, 'serie')->hiddenInput(['value'=>$serie])->label(false); 
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php  
                        echo $form->field($model->ordenServicio, 'cliente')->widget(Select2::classname(), [
                            'data' => $clientes,
                            'language' => 'es',
                            'options' => ['placeholder' => '-Seleccionar Cliente-', 
                                            'onchange'=>'
                                            $.getJSON("'.Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id=').'"+$(this).val(), function (jsondata) {
                                                //console.log(jsondata);
                                                document.getElementById("ordenservicio-vehiculo_id").innerHTML = jsondata;
                                            });

                                            /*$.post( "'.Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id=').'"+$(this).val(), function( data ) {
                                                document.getElementById("ordenservicio-vehiculo_id").innerHTML = data;
                                                console.log( data );
                                            });*/ ' ],
                            'pluginOptions' => [
                                'allowClear' => false
                            ],
                        ])->label('Cliente').'&nbsp;&nbsp;';  ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $form->field($model->ordenServicio, 'vehiculo_id')->widget(Select2::classname(), [
                            'data' =>  Array(),
                            'language' => 'es',
                            'options' => ['placeholder' => '-Seleccionar Vehículo-'],
                            'pluginOptions' => [
                                'allowClear' => false
                            ], ])->label('Vehículo').'&nbsp;&nbsp;';   //echo $form->field($model->ordenServicio, 'vehiculo_id')->dropDownList([''=>''])->label('Vehículo').'&nbsp;&nbsp;'; ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            $user_rol = Yii::$app->session->get('user_rol');
                            if($user_rol == UserRole::ROL_GESTOR_AREA)
                                echo $form->field($model->ordenServicio, 'area_id')->hiddenInput(['value' => $area_id])->label(false);
                            else
                                echo $form->field($model->ordenServicio, 'area_id')->dropDownList($areas);
                        ?>
                    </div>
                </div>
                    <?php
                        echo $form->field($model->ordenServicio, 'estado_orden_id')->hiddenInput(['value' => $estado_id])->label(false); 
                        echo $form->field($model->ordenServicio, 'fecha_iniciada')->hiddenInput(['value' => date("Y-m-d")])->label(false);
                    ?>
            </fieldset>
        </div>
        <?php
            echo Html::submitButton('Guardar', ['class' => 'btn btn-success']); 
            ActiveForm::end(); ?>
    </div>
</div>
