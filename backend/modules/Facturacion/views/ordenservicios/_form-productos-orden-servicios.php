<?php
use yii\helpers\Html;

//echo Html::checkboxList('roles', [16, 42], $lista_productos);
?>

<td>
    
    <!-- FIELD EXISTENCIA, para usar en las validaciones 
    <input id="existencia_prod_<?= $key ?>_"  
        type="hidden"
        class="form-control prod_input"
        name="ProductosOrdenServicios[<?= $key ?>][existencia]" 
        value="<?php //if(isset($productosOrden->producto->existencia)) echo $productosOrden->producto->existencia; ?>">
-->
    <!-- FIELD NOMBRE -->
    <input id="prod_<?= $key ?>_"  
        placeholder="Seleccionar..." type="text"
        class="form-control prod_input"
        value="<?php if(isset($productosOrden->producto->nombre)) echo $productosOrden->producto->nombre; ?>">
</td>
<td>
    <!-- FIELD CANTIDAD -->
    <?= $form->field($productosOrden, 'cant_productos')->textInput([
    'id' => "cant_prod_{$key}_",
    'name' => "ProductosOrdenServicios[$key][cant_productos]",
    'type' => 'number',
    'style'=> 'max-width:100px',
    'step'=>'any',
    ])->label(false) ?>
</td>
<td>
    <!-- FIELD PRECIO -->
    <?= $form->field($productosOrden, 'precio')->textInput([
    'id' => "precio_prod_{$key}_",
    'name' => "ProductosOrdenServicios[$key][precio]",
    'type' => 'number',
    'style'=> 'max-width:100px',
    'step'=>'any',
    ])->label(false) ?>
</td>
<td>
    <?= Html::a('Eliminar', 'javascript:void(0);', [
    'class' => 'remove-prodOrden-btn btn btn-default btn-xs',
    ]) ?>

<?php 
        //ID FIELD    
        echo $form->field($productosOrden, 'producto_id')->hiddenInput([
            'id' => "id_prod_{$key}_",
            'name' => "ProductosOrdenServicios[$key][producto_id]",
            'type' => 'hidden',
        //  'value' => "",
        ])->label(false);
    ?>
    <?php 
        //Campo para guardar el id Original, en caso de que se cambie el producto, para poder identificar el anterior que estaba y poder retirarlo de la RESERVA    
        echo $form->field($productosOrden, 'producto_id_old')->hiddenInput([
            'id' => "id_prod_{$key}_old",
            'name' => "ProductosOrdenServicios[$key][producto_id_old]",
            'type' => 'hidden',
            'value' => !$productosOrden->isNewRecord ? $productosOrden->producto_id : -1,
        ])->label(false);
    ?>
</td>