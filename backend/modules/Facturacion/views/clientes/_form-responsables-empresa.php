<?php
use yii\helpers\Html;
?>

<td>
    <?php echo $form->field($responsable, 'nombre')->textInput([
        'id' => "Responsable_{$key}_nombre",
        'name' => "EmpresaResponsables[$key][nombre]",
        'aria-required' => 'false'
        ])->label(false) ?>
</td>
<td>
    <?php echo $form->field($responsable, 'telefono')->textInput([
        'id' => "Responsable_{$key}_telefono",
        'name' => "EmpresaResponsables[$key][telefono]",
            ])->label(false) ?>
</td>
<td>
    <?php echo $form->field($responsable, 'ci')->textInput([
        'id' => "Responsable_{$key}_ci",
        'name' => "EmpresaResponsables[$key][ci]",
            ])->label(false) ?>
</td> 
<td>
    <?php echo $form->field($responsable, 'email')->textInput([
        'id' => "Responsable_{$key}_email",
        'name' => "EmpresaResponsables[$key][email]",
            ])->label(false) ?>
</td>
<td>
<?= Html::a('Eliminar', 'javascript:void(0);', [
'class' => 'remove-responsable-btn btn btn-default btn-xs',
]) ?>
</td>