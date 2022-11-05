<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use backend\modules\Reportes\models\ActaEntrega;
use yii\helpers\Url;
?>

<legend>
    <h3>Acta de Entrega</h3>
</legend>

<div class="actaEntrega-form">
    <?php $form = ActiveForm::begin(['id' => 'form-acta-entrega', 'options'=>['target'=>'_blank']]); ?>
    <div class="row">
        <div class="col-md-6">  
            <?= $form->field($model, 'cliente')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">  
            <?= $form->field($model, 'no_contrato')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <legend>Vehículo</legend>
    <div class="row">
        <div class="col-md-4">  
            <?= $form->field($model, 'marca')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'modelo')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">  
            <?= $form->field($model, 'matricula')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
            <?= $form->field($model, 'servicio')->textarea(['maxlength' => true]) ?>

            <?= $form->field($model, 'garantia')->textarea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Imprimir', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php /*ob_start(); // output buffer the javascript to register later ?>
    <script>
        $("#form-acta-entrega").on('submit', function() {
            console.log("sadasdasd");
        });
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); */ ?>