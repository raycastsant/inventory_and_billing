<?php
use kartik\file\FileInput;
use yii\helpers\Html;
use kartik\select2\Select2;
//use kartik\number\NumberControl;
?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'tipoproducto_id')->widget(Select2::classname(), [
                'data' =>  $tipoproductos,
                'language' => 'es',
                'pluginOptions' => [
                    'allowClear' => false
                ], ])->label('Categoría'); //$form->field($model, 'tipoproducto_id')->dropDownList($tipoproductos)->label('Tipo'); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'unidad_medida_id')->widget(Select2::classname(), [
                'data' =>  $unidad_medidas,
                'language' => 'es',
                'pluginOptions' => [
                    'allowClear' => false
                ], ])->label('Unidad de medida');   //$form->field($model, 'unidad_medida_id')->dropDownList($unidad_medidas)->label('Unidad de medida'); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?php 
            $dispOptions = ['class' => 'form-control kv-monospace'];
            echo $form->field($model, 'costo')/*->widget(NumberControl::classname(), [
                'maskedInputOptions' => [
                    'allowMinus' => false,
                ],
            ])*/->textInput(['type'=>'number', 'step'=>'any']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'precio')->textInput(['type'=>'number', 'step'=>'any']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'codigo')->textInput(['maxlength' => true])->label('Código') ?>
    </div>
</div>
<div class="row">
  <?php 
  if(!isset($isajax))
    $isajax = false;

  if(!$isajax) { ?>
    <div class="col-md-4">
        <?= $form->field($model, 'existencia')->textInput(['type'=>'number', 'step'=>'any']) ?>
    </div>
<?php  } ?>
    <div class="col-md-3">
        <?= $form->field($model, 'stock_minimo')->textInput(['type'=>'number']) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'desc')->textarea(['rows' => 4]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'desc_ampliada')->textarea(['rows' => 7]) ?>
    </div>
    <div class="col-md-6">
        <?php
       // $id = Html::getInputId($model, 'image_deleted');
           $options = ['options' => ['accept' => 'image/*'], 
                       'pluginOptions' => ['showUpload' => false, 'removeLabel' => '', 'browseLabel' => '',
                                            'captionLabel'=>'Seleccionar imagen'],  'language'=>'es'
                      ];

           if ($model->nombre_imagen != null) {
               $options['pluginOptions']['initialPreview'] = [Html::img(Yii::$app->request->baseUrl.'\\uploads\\'.$model->nombre_imagen 
                    , ['class' => 'file-preview-image  kv-preview-data']
               )];
               $options['pluginOptions']['overwriteInitial'] = true;
           }

          // echo $form->field($model, 'image_deleted', ['template' => '{input}'])->hiddenInput();
           echo $form->field($model, 'imagefile')->widget(FileInput::classname(), $options);
           echo "<!-- Nombre Imagen -->";
           echo $form->field($model, 'nombre_imagen')->hiddenInput()->label(false);

        /*    echo $form->field($model, 'imagefile')->widget(FileInput::classname(), [
                'options' => ['accept' => 'image/*', 'data-show-upload'=>'false', 
                                'data-allowed-file-extensions'=>['png', 'jpeg', 'jpg', 'gif'], 
                                'initialPreviewAsData'=>'true'
                            ],
                        ])*/ ?>
    </div> 

    <?php ob_start(); // output buffer the javascript to register later ?>
        <script>
            //Para el boton de eliminar la imagen
            var x = $(document).find('.fileinput-remove');
            if(x.length > 0) {
                x[0].hidden = true;
                if($(document).find('.kv-file-remove').length > 0) {
                    $(document).on('click', '.kv-file-remove', function () {
                        x[0].click();
                        $('#producto-nombre_imagen')[0].value = "";
                    });
                }
            };
           
        </script>
    <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
</div>