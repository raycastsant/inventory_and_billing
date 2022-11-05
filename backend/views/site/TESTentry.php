<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    ?>

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->label('Tu nombre') ?>
        <?= $form->field($model, 'email')->label('Tu correo') ?>
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
<?php ActiveForm::end(); ?>