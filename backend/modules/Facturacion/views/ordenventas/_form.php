<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Facturacion\models\OrdenVenta;
use backend\modules\Facturacion\models\ProductosOrdenVenta;
use backend\modules\Facturacion\models\ServicioTrabajador;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use backend\components\UserRole;
use kartik\select2\Select2;

//Modal para la selección del producto
Modal::begin([
    'header' => '<h3>Seleccionar Producto</h3>',
    'toggleButton' => ['label' => '', 'id' => 'modal_launch', 'style' => 'display:none'],
    'id' => 'modal_table',
    'closeButton' => ['id' => 'close_modal'],
    //'style' => ['max-height'=>'400px']
]);
echo '<div class="table-responsive" style="max-height:480px">';
echo '<table id="tproductos" class="display table table-striped table-hover" cellspacing="0" width="100%">';
echo    '<thead>';
echo        '<tr>';
echo '<th>Código</th>';
echo  '<th>Nombre</th>';
echo   '<th>ID</th>';
echo   '<th>Existencia</th>';
echo   '<th>Precio</th>';
echo    '<th>&nbsp;</th>';
echo '</tr>';
echo '</thead>';
echo '</table>';
echo  '</div>';
// echo '</div>';
Modal::end();
?>

<div class="orden-venta-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]);
    echo Html::submitButton('Guardar', ['class' => 'btn btn-success pull-right']);
    ?>

    <?= $model->errorSummary($form); ?>
    <!-- Form de datos de la orden -->
    <div class="well">
        <fieldset>
            <div class="row">
                <div class="col-md-3">
                    <?php
                    echo $form->field($model->ordenVenta, 'codigo')->textInput(['maxlength' => true, 'readonly' => 'readonly'])->label('Código') . '&nbsp;&nbsp;';  ?>
                </div>
                <div class="col-md-3">
                    <?php
                    echo $form->field($model->ordenVenta, 'cliente_id')->widget(Select2::class, [
                        'data' => $clientes,
                        'language' => 'es',
                        'options' => [
                            'placeholder' => '-Seleccionar Cliente-',
                        ],
                        'pluginOptions' => [
                            'allowClear' => false
                        ],
                    ])->label('Cliente') . '&nbsp;&nbsp;';  ?>
                </div>
                <div class="col-md-3">
                    <?php
                    $user_rol = Yii::$app->session->get('user_rol');
                    if ($user_rol == UserRole::ROL_GESTOR_AREA)
                        echo $form->field($model->ordenVenta, 'area_id')->hiddenInput(['value' => $area_id])->label(false);
                    else
                        echo $form->field($model->ordenVenta, 'area_id')->dropDownList($areas);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model->ordenVenta, 'monto_adicional')->textInput(['type' => 'number', 'step' => 'any'])->label('Monto adicional')  ?>
                </div>
                <div class="col-md-9">
                    <?= $form->field($model->ordenVenta, 'monto_adicional_desc')->textInput()->label('Descripción del servicio')  ?>
                </div>
            </div>

            <?php
            echo $form->field($model->ordenVenta, 'estado_orden_id')->hiddenInput(['value' => $estado_id])->label(false);
            echo $form->field($model->ordenVenta, 'fecha_iniciada')->hiddenInput(['value' => date("Y-m-d")])->label(false);
            /*echo $form->field($model->ordenVenta, 'estado_orden_id')->hiddenInput(['value' => $estado_id])->label(false); 
                $fechaini =  date("Y-m-d");
                echo $form->field($model->ordenVenta, 'fecha_iniciada')->hiddenInput(['value' => $fechaini])->label(false);*/
            ?>
        </fieldset>
    </div>
    <br>
    <div class="row">
        <!-- Form de seleccion de productos -->
        <div id="productsForm" class="well">
            <fieldset>
                <legend>Productos utilizados
                    <?php
                    echo Html::a('Insertar producto', 'javascript:void(0);', [
                        'id' => 'add-product-btn',
                        'class' => 'pull-right btn btn-primary btn-small'
                    ])   ?>
                </legend>

                <?php
                $productosOrden = new ProductosOrdenVenta();
                $productosOrden->loadDefaultValues();
                echo '<div class="table-responsive">';
                echo '<table id="productos-orden" class="table table-condensed">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Producto</th>';
                echo '<th>Cantidad</th>';
                echo '<th>Precio</th>';
                echo '<td>&nbsp;</td>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($model->productosOrdenVentas as $key => $_prodsOrdens) {
                    echo '<tr>';
                    echo $this->render('_form-productos-orden-ventas', [
                        'key' => $_prodsOrdens->isNewRecord ? (strpos($key, 'new') !== false ? $key : 'new' . $key) : $_prodsOrdens->id,
                        'form' => $form,
                        'productosOrden' => $_prodsOrdens,
                    ]);
                    echo '</tr>';
                }

                echo '<tr id="new-producto-orden-block" style="display: none;">';
                echo $this->render('_form-productos-orden-ventas', [
                    'key' => '__id__',
                    'form' => $form,
                    'productosOrden' => $productosOrden,
                ]);

                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                ?>
            </fieldset>
        </div>
    </div>

    <?php ob_start(); // output buffer the javascript to register later 
    ?>
    <script>
        //-------------------------- Scripts de Productos ----------------------------//     
        // add producto button
        var prodOrden_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
        $('#add-product-btn').on('click', function() {
            prodOrden_k += 1;
            $('#productos-orden').find('tbody')
                .append('<tr>' + $('#new-producto-orden-block').html().replace(/__id__/g, 'new' + prodOrden_k) + '</tr>');
            document.getElementById('cant_prod_new' + prodOrden_k + '_').value = '1';
        });

        // remove producto button
        $(document).on('click', '.remove-prodOrden-btn', function() {
            $(this).closest('tbody tr').remove();
        });

        <?php
        // OPTIONAL: click add when the form first loads to display the first new row
        if (!Yii::$app->request->isPost && $model->ordenVenta->isNewRecord)
            echo "$('#add-product-btn').click();";
        ?>

        var table,
            $input,
            hiddenId,
            $Hinput;

        //Método de llenado de los datos de los productos
        fill_datatable();

        function fill_datatable(filter = '') {
            table = $('#tproductos').DataTable({
                "processing": true,
                "serverSide": true,
                //"bLengthChange" : false,
                "order": [],
                "searching": false,
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ filas",
                    "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "infoPostFix": "",
                    "loadingRecords": "Cargando...",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "No hay datos",
                    "paginate": {
                        "first": "Primero",
                        "previous": "«",
                        "next": "»",
                        "last": "Último",
                    },
                },
                "ajax": {
                    url: "<?= Url::toRoute('/inventario/productos/getajaxproducts'); ?>",
                    type: "POST",
                    data: {
                        filterVal: filter,
                        //   almacen:1
                    }
                },
                "columnDefs": [{
                    "targets": [2],
                    "visible": false,
                    "searchable": false
                }, ]
            });

            //Para esto modifique la libreria datatables.min.js y agregue pageSize a un div que salia vacio para utilizarlo y y mostrar el componente de busqueda
            $('.pageSize')[0].innerHTML = '<label>Buscar:</label><input type="search" id="product_search" class="form-control form-control-sm" value="' + filter + '" placeholder="Filtrar valor" aria-controls="tproductos">'

            //Search products Table filter
            $('#product_search').on('keyup', function() {
                var f = $(this).val();
                if (f != '') {
                    $('#tproductos').DataTable().destroy();
                    fill_datatable(f);
                } else {
                    $('#tproductos').DataTable().destroy();
                    fill_datatable();
                }
            });

            setInputCursor($('#product_search')[0]);
        };

        //Mostrar dialogo de seleccion de productos
        $(document).on('click', '.prod_input', function() {
            if ($('#modal_table').hasClass('in') == true) {
                $input = this;
                $('#modal_launch').click();
            }
        });
        $(document).on('focus', '.prod_input', function() {
            if ($('#modal_table').hasClass('in') == false) {
                $input = this;
                $('#modal_launch').click();
            }
        });

        //Table Row selection Event
        $('#tproductos tbody').on('click', 'tr', function() {
            $(this).toggleClass('selected');

            $input.value = table.rows('.selected').data()[0][1]; //Nombre Producto
            document.getElementById('id_' + $input.id).value = table.rows('.selected').data()[0][2]; //Id del producto
            document.getElementById('precio_' + $input.id).value = table.rows('.selected').data()[0][4]; //Precio del producto

            $(this).toggleClass('selected');
            $('#close_modal').click();
        });
    </script>
    <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>

    <?php ActiveForm::end(); ?>
</div>