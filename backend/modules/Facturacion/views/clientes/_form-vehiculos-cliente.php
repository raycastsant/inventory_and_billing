<?php
use yii\helpers\Html;
use kartik\select2\Select2;
/*
echo $form->field($vehiculo, 'anno')->widget(Select2::classname(), [
    'data' =>  $annos,
    'id' => "Vehiculoshghj_anno",
    'name' => "Vehiculos[5][anno]",
    'options' => ['placeholder' => '-Seleccionar-'],
    'language' => 'es',
    'pluginOptions' => [
        'allowClear' => false
    ], ])->label(false);*/
?>

<td>
<?php 
    //ID FIELD    
   /* echo $form->field($vehiculo, 'cliente_id')->hiddenInput([
        'id' => "Vehiculos_{$key}_cliente",
        'name' => "Vehiculos[$key][cliente_id]",
        'type' => 'hidden',
        ])->label(false);
   */ ?>
    <?php echo $form->field($vehiculo, 'chapa')->textInput([
        'id' => "Vehiculos_{$key}_chapa",
        'name' => "Vehiculos[$key][chapa]",
        'aria-required' => 'false'
        ])->label(false) ?>
</td>
<td>
    <?php echo $form->field($vehiculo, 'modelo')->textInput([
        'id' => "Vehiculos_{$key}_modelo",
        'name' => "Vehiculos[$key][modelo]",
            ])->label(false) ?>
</td>
<td>
    <?php echo $form->field($vehiculo, 'marca')->textInput([
        'id' => "Vehiculos_{$key}_marca",
        'name' => "Vehiculos[$key][marca]",
            ])->label(false) ?>
</td>
 <td>
 <?php echo $form->field($vehiculo, 'fabricante')->textInput([
        'id' => "Vehiculos_{$key}_fabricante",
        'name' => "Vehiculos[$key][fabricante]",
            ])->label(false) ?>
</td> 
<td>
<?php /*echo $form->field($vehiculo, 'anno')->widget(Select2::classname(), [
            'data' =>  $annos,
            'id' => "Vehiculos_{$key}_anno",
            'name' => "Vehiculos[$key][anno]",
            'options' => ['placeholder' => '-Seleccionar-'],
            'language' => 'es',
            'pluginOptions' => [
                'allowClear' => false
            ], ])->label(false);*/

    echo $form->field($vehiculo, 'anno')->textInput([
        'id' => "Vehiculos_{$key}_anno",
        'name' => "Vehiculos[$key][anno]",
            ])->label(false) ?>
</td> 
<td>
 <?php echo $form->field($vehiculo, 'codigo_motor')->textInput([
        'id' => "Vehiculos_{$key}_codigo_motor",
        'name' => "Vehiculos[$key][codigo_motor]",
            ])->label(false) ?>
</td> 
<td>
 <?php echo $form->field($vehiculo, 'codigo_alternador')->textInput([
        'id' => "Vehiculos_{$key}_codigo_alternador",
        'name' => "Vehiculos[$key][codigo_alternador]",
            ])->label(false) ?>
</td> 
<td>
<?= Html::a('Eliminar', 'javascript:void(0);', [
'class' => 'remove-vehiculo-btn btn btn-default btn-xs',
]) ?>
</td>