<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Reportes\models\ActaEntrega;
use yii\helpers\Url;

?>

<legend>
    <h3>Orden de Servicio</h3>
</legend>
<div class="actaEntrega-form">
    <?php $form = ActiveForm::begin(['id' => 'form-acta-servicio', 'options' => ['target' => '_blank']]); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'cliente')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'no_contrato')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'tiempo')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'precio')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'servicio')->textarea(['maxlength' => true]) ?>
        </div>
    </div>


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