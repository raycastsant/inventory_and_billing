<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Producto */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="producto-form well">

    <?php $form = ActiveForm::begin([ 'options'=>['enctype' => 'multipart/form-data'], ]); ?>

    <div class="modal inmodal" id="ConfirmDialog" role="dialog" data-keyboard="false" style="max-width:500px">
        <div class="modal-dialog modal-md"></div>
        <div class="modal-content animated bounceInTop">
          <div class="modal-header">
            <h4 class="modal-title text-left">Escriba el motivo por el que cambió la existencia del producto</h4>
          </div>
          <div class="modal-body"> 
            <?php echo $form->field($model, 'trazacambio')->textInput()->label(false) ?>
            <button type="button" id="okBtn" class="btn btn-success">Aceptar</button>
          </div>
        </div>
    </div> 

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    //Para el create
   /* if(isset($almacen))
      echo $form->field($model, 'almacen_id')->hiddenInput(['value' => $almacen->id])->label(false);*/ 

        echo $this->render('_form_fields', ['form'=>$form, 'model'=>$model, 'tipoproductos'=>$tipoproductos, 'unidad_medidas'=>$unidad_medidas]);
      ?>
    <br>
    <?php ActiveForm::end(); ?>

    <?php 
      if($isUpdate) {
          ob_start(); // output buffer the javascript to register later ?>
            <script>
              var oldExistencia = $('#producto-existencia').val();
                  $('#producto-existencia').focusout(function () {
                      if(oldExistencia !=  $('#producto-existencia').val()) {
                        oldExistencia = $('#producto-existencia').val();
                        $('#ConfirmDialog').modal({backdrop:'static'});
                      }
                  });

                 //Confirm button del dialog de motivo de cambio de Existencia 
                  $('#okBtn').on('click', function () {
                    var str = $('#producto-trazacambio').val();
                    if(str.trim().length > 25) {
                      $('#ConfirmDialog').modal('hide');
                    }
                    else{
                      $('#producto-trazacambio').closest('.field-producto-trazacambio')[0].classList.add("has-error");
                      $('#producto-trazacambio').closest('.field-producto-trazacambio')[0].children[1].innerHTML = "Escriba un valor de al menos 25 caracteres";
                      //console.log( $('#producto-trazacambio').closest('.field-producto-trazacambio')[0].children);
                    }
                  });

                  $('#w0').on('submit', function (e) {
                    return ($('#ConfirmDialog').hasClass('in') != true);
                  });

                  $('#producto-existencia').on('keypress', function (e) {
                      var keycode = e.which;
                      if(keycode == 13) {
                        e.preventDefault();
                        return false;
                      }
                  });
                  
            </script>
        <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); 
      }?>
</div>
