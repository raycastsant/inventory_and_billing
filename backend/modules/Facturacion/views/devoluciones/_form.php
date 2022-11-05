<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Facturacion\models\ProductosOrdenVenta;
use yii\helpers\Url;
//use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Devolucion */
/* @var $form yii\widgets\ActiveForm */

    $tipoOrden = "ventas";
    $returnUrl = "ordenventas";
    if(!$modelForm->devolucion->is_venta) {
        $tipoOrden = "servicios";
        $returnUrl = "ordenservicios";
    }
?>

<legend><?= 'Orden de '.$tipoOrden.': '.Html::encode($modelForm->devolucion->getOrden()->codigo) ?></legend>

<div class="devolucion-form">

    <?php 
       /* $this->registerJs('
                function validateForm() {
                    window.history.back();
                    return false;
                }
            ',
            yii\web\View::POS_READY);*/

        $form = ActiveForm::begin(['id'=>'devolucion_form', 
                                     'action'=>Url::toRoute('/facturacion/devoluciones/create'), 
                            //         'options' => ['onsubmit' => 'validateForm();']
                                     ]); 
        echo $form->field($modelForm->devolucion, 'ordenId')->hiddenInput(['value' => $modelForm->devolucion->ordenId])->label(false); 
        echo $form->field($modelForm->devolucion, 'is_venta')->hiddenInput(['value' => $modelForm->devolucion->is_venta])->label(false); 
        echo $form->field($modelForm->devolucion, 'fecha')->hiddenInput(['value' => $modelForm->devolucion->fecha])->label(false); 
        echo $form->field($modelForm->devolucion, 'parcial')->hiddenInput(['value' => $modelForm->devolucion->parcial])->label(false); 
    ?>

    <?= $modelForm->errorSummary($form); ?>

    <div id="productsForm" class="well">
        <fieldset>
            <legend>Seleccionar productos devueltos</legend>
        <?php
            echo '<div class="table-responsive">';
                echo '<table id="productos-orden" class="table table-condensed">';
                echo '<thead>';
                echo '<tr>';
                    echo '<th>Seleccionado</th>';
                    echo '<th>Producto</th>';
                    echo '<th>Cantidad</th>';
                    echo '<td>&nbsp;</td>';
                echo '</tr>';
                echo '</thead>';
                    echo '<tbody>';
                    
                    if($modelForm->devolucion->is_venta) {
                        //Ventas
                        foreach ($modelForm->devolucion->getOrden()->productosOrdenVentas as $key => $_prodsOrdens) {
                            echo '<tr>';
                            echo $this->render('_form-productos-orden-ventas', [
                            'key' => $_prodsOrdens->isNewRecord ? (strpos($key, 'new') !== false ? $key : 'new' . $key) : $_prodsOrdens->id,
                            'form' => $form,
                            'productosOrden' => $_prodsOrdens,
                            ]);
                            echo '</tr>';
                        }
                    }
                    else {
                        //Servicios
                        foreach ($modelForm->devolucion->getOrden()->productosOrdenServicios as $key => $_prodsOrdens) {
                            echo '<tr>';
                            echo $this->render('_form-productos-orden-servicios', [
                            'key' => $_prodsOrdens->isNewRecord ? (strpos($key, 'new') !== false ? $key : 'new' . $key) : $_prodsOrdens->id,
                            'form' => $form,
                            'productosOrden' => $_prodsOrdens,
                            ]);
                            echo '</tr>';
                        }
                    }
                    
                    echo '</tr>';
                    echo '</tbody>';
                echo '</table>';
            echo '</div>';
        ?>
        </fieldset>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <a class="btn btn-default" href="<?= Url::toRoute("/facturacion/".$returnUrl."/view") ?>?id=<?= $modelForm->devolucion->ordenId ?>">Cancelar</a>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php ob_start(); ?>
    <script> 
        //Evento del checkbox Todos   
        $(".select_all").on('click', function() {
            var id = "cant_prod_"+$(this)[0].id+"_";
            //var input = $("#"+id)[0]; //document.getElementById(id);

            //$("#"+id)[0].readonly = $(this)[0].checked;
            $("#"+id).prop("readonly", $(this)[0].checked);
            $("#"+id).prop("value", $("#"+id)[0].max);
        });

        //Evento del checkbox Seleccionado
        $(".selected").on('click', function() {
            if($(this)[0].checked === true)
                $(this).prop("value", 1);
            else
                $(this).prop("value", 0);
        });

        //Validar que se seleccione al menos un producto
        $(document).on('submit', "#devolucion_form", function() {
            var almostOne = false;
            $(".selected").each(function() {
                if($(this)[0].checked === true) {
                    almostOne = true;
                    return true;
                }
            });

            if(almostOne === false) {
                window.alert("Debe seleccionar al menos un producto a devolver");
                return false;
            }
        });

    </script> 
 <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>