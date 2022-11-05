<?php
    use yii\helpers\Html;
?>

<td>
    <!-- FIELD SELECCIONADO -->
    <input type="checkbox" id="selected_<?= $key ?>_" class="selected" name="DevolucionVentas[<?= $key ?>][seleccionado]" value="0">
    
    <?php /* $form->field($productosOrden, 'seleccionado')->label(false)->checkbox([
            'id' => "selected_{$key}_",
            'name' => "DevolucionVentas[$key][seleccionado]",
            'class' => 'selected',] 
        , false) */
    ?>
</td>
<td>
    <?php 
    //ID FIELD    
    echo $form->field($productosOrden, 'producto_id')->hiddenInput([
        'id' => "id_prod_{$key}_",
        'name' => "DevolucionVentas[$key][producto_id]",
        'type' => 'hidden',
      //  'value' => "",
        ])->label(false);
    ?>

    <!-- FIELD NOMBRE -->
    <input id="prod_<?= $key ?>_"  
        type="text"
        class="form-control prod_input" 
        readonly="readonly"
        value="<?php if(isset($productosOrden->producto->nombre)) echo $productosOrden->producto->nombre; ?>">
</td>
<td>
    <div class="row">
        <div class="col-md-3">
            <!-- FIELD CANTIDAD -->
            <?= $form->field($productosOrden, 'cantidad')->textInput([
            'id' => "cant_prod_{$key}_",
            'name' => "DevolucionVentas[$key][cantidad]",
            'type' => 'number',
            'title' => 'Seleccionar la cantidad en caso de no devolver todo el producto',
            'min' => 0.01,
            'max' => $productosOrden->cantidad,
            'style'=> 'max-width:100px',
            'readonly' => 'readonly',
            'step'=>'any',
            ])->label(false) ?>
        </div>
        <div class="col-md-8">
            <label>
                <input type="checkbox" title="Devolver todo el producto" class="select_all"  id="<?= $key?>" checked="true" value="1" aria-invalid="false">Todo</label>
        </div>
    </div>
</td>