<?php
use yii\helpers\Html;
?>

<td>
    <?= $form->field($serviciosOrden, 'servicio_id')->dropDownList($servicios, [
    'id' => "ServicioTrabajadors_{$key}_servicio_id",
    'name' => "ServicioTrabajadors[$key][servicio_id]",
    ])->label(false) ?>
</td>
<td>
    <?= $form->field($serviciosOrden, 'trabajador_id')->dropDownList($trabajadores,[
    'id' => "ServicioTrabajadors_{$key}_trabajador_id",
    'name' => "ServicioTrabajadors[$key][trabajador_id]",
    ])->label(false) ?>
</td>
<!-- <td>
    <?php /*echo $form->field($serviciosOrden, 'fecha')->textInput([
    'id' => "ServicioTrabajadors_{$key}_fecha",
    'name' => "ServicioTrabajadors[$key][fecha]",
    'type' => 'date',
    'min' => date("Y-m-d"),
    ])->label(false)*/ ?>
</td> -->
<td>
    <?= $form->field($serviciosOrden, 'precio')->textInput([
    'id' => "ServicioTrabajadors_{$key}_precio",
    'name' => "ServicioTrabajadors[$key][precio]",
    'type' => 'number',
    'step'=>'any',
    'style' => 'max-width:100px'
    ])->label(false) ?>
</td>
<td>
<?= Html::a('Eliminar', 'javascript:void(0);', [
'class' => 'remove-servOrden-btn btn btn-default btn-xs',
]) ?>
</td>