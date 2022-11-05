<?php
use yii\helpers\Html;
?>

<td>
    <?php 
    //ID FIELD    
    echo $form->field($ocp, 'producto_id')->hiddenInput([
        'id' => "id_prod_{$key}_",
        'name' => "OrdenCompraProducto[$key][producto_id]",
        'type' => 'hidden',
        ])->label(false);
    ?>
    <input id="prod_<?= $key ?>_"  
        placeholder="Seleccionar..." type="text"
        class="form-control prod_input" 
        value="<?php if(isset($ocp->producto->nombre)) echo $ocp->producto->nombre; ?>">
</td>
<td>
    <!-- FIELD Codigo -->
    <input id="codigo_prod_<?= $key ?>_"  
        type="text" 
        class="form-control" 
        value="<?php if(isset($ocp->producto->codigo)) echo $ocp->producto->codigo; ?>" readonly>
</td>
<td>
    <!-- FIELD CANTIDAD -->
    <?= $form->field($ocp, 'cantidad')->textInput([
    'id' => "cant_prod_{$key}_",
    'name' => "OrdenCompraProducto[$key][cantidad]",
    'type' => 'number',
    'style'=>"width:100px",
    'step'=>'any',
    ])->label(false) ?>
</td>
<td>
    <!-- FIELD Costo -->
    <?= $form->field($ocp, 'costo')->textInput([
    'id' => "costo_prod_{$key}_",
    'name' => "OrdenCompraProducto[$key][costo]",
    'type' => 'number',
    'step'=>'any',
    'style'=>"width:100px" 
    ])->label(false) ?>
</td>
<td>
    <?= Html::a('Eliminar', 'javascript:void(0);', [
    'class' => 'remove-OCP-btn btn btn-default btn-xs',
    ]) ?>
</td>