<?php
    use yii\helpers\Html;
?>

<td>
    <!-- FIELD SELECCIONADO -->
    <input type="checkbox" id="selected_<?= $key ?>_" class="selected" name="DevolucionServicios[<?= $key ?>][seleccionado]" value="0">
</td>
<td>
    <?php 
    //ID FIELD    
    echo $form->field($productosOrden, 'producto_id')->hiddenInput([
        'id' => "id_prod_{$key}_",
        'name' => "DevolucionServicios[$key][producto_id]",
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
            <?= $form->field($productosOrden, 'cant_productos')->textInput([
            'id' => "cant_prod_{$key}_",
            'name' => "DevolucionServicios[$key][cantidad]",
            'type' => 'number',
            'title' => 'Seleccionar la cantidad en caso de no devolver todo el producto',
            'min' => 0.01,
            'max' => $productosOrden->cant_productos,
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