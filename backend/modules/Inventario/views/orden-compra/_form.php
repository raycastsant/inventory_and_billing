<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Inventario\models\OrdenCompraProducto;
use yii\bootstrap\Modal;
use yii\helpers\Url;

   //Modal que muestra la lista de productos
    Modal::begin([
        'header' => '<h3>Seleccionar Producto</h3>',
        'toggleButton' => ['label'=>'', 'id'=>'modal_launch', 'style'=>'display:none'],
        'id' => 'modal_table',
        'closeButton'=>['id'=>'close_modal'],
        //'style' => ['max-height'=>'400px']
    ]); 
       // echo    '<label>Buscar:';
            //echo '<input type="search" id="product_search" class="form-control form-control-sm" placeholder="" aria-controls="tproductos">';
      //      echo '</label>';
            echo '<div class="table-responsive" style="max-height:480px">';
            echo '<table id="tproductos" class="display table table-striped table-hover" cellspacing="0" width="100%">';
            echo    '<thead>';
            echo        '<tr>';
                      echo '<th>Código</th>';
                      echo  '<th>Nombre</th>';
                      echo   '<th>ID</th>'; 
                      echo   '<th>Existencia</th>';                             
                      echo   '<th>Costo (cup)</th>';    
                      echo    '<th>&nbsp;</th>';
                      echo '</tr>';
                      echo '</thead>';
            echo '</table>';
            echo  '</div>';
   Modal::end();
?>
<!-- Para recargar la lista de productos desde la Orden de Compra -->
<button id="tableProdReloader" class="hidden tableProdReloader"></button> 

<div id="success-msg" class="alert-success alert fade in" style="opacity: 3; display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
</div>

<div class="modal inmodal" id="NewProductWindow" role="dialog" data-keyboard="false">
    <div class="modal-dialog modal-md"  style="min-width:800px"></div>
</div> 

<div class="ordenCompra-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options'=>['class'=>'form-inline']]); ?>
    <?= $model->errorSummary($form); ?>

    <div> 
        <?php $fecha =  date("Y-m-d");
              echo $form->field($model->ordenCompra, 'fecha_creada')->hiddenInput(['value' => $fecha])->label(false);
              $date = new DateTime();
              echo $form->field($model->ordenCompra, 'codigo')->textInput(['style'=>'max-width:200px', 
                                                                           'value'=>$date->format('Y').'/000'.$serie, 
                                                                           'readonly'=>'readonly']);
        ?>
        <!-- Form de seleccion de los productos -->
        <div id="ordenForm" class="well">
        <fieldset>
        <legend>
            <?php
            echo Html::submitButton('Guardar', ['class' => 'btn btn-success', 'id'=>'saveBtn']);
           /* echo Html::a('<span class="glyphicon glyphicon-qrcode"></span> Agregar nuevo producto', ['#'], 
                            ['class' => 'btn btn-default quick-add-product pull-right', 
                            // 'data-toggle'=>'modal',
                             //'data-target'=>'#NewProductWindow'
                             'id' => 'AddProdBtn'
                            ]); */
                             ?>
        <a id="AddProdBtn" class="btn btn-default quick-add-product pull-right" href="javascript:void(0);">
            <span class="glyphicon glyphicon-qrcode"></span> Agregar nuevo producto
        </a>

        </legend>
        <?php
            $OCP = new OrdenCompraProducto();
            $OCP->loadDefaultValues();
            echo '<div class="table-responsive">';
                echo '<table id="productos" class="table table-condensed table-bordered">';
                echo '<thead>';
                echo '<tr>';
                    echo '<th>Producto</th>';
                    echo '<th>Código</th>';
                    echo '<th>Cantidad</th>';
                    echo '<th>Costo</th>';
                    echo '<td>'.Html::a('<span class="glyphicon glyphicon-plus"', 'javascript:void(0);', [
                        'id' => 'add-producto-btn',
                        'class' => 'btn btn-primary btn-small',
                        'title' => 'Insertar producto'
                        ]).'</td>';
                echo '</tr>';
                echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($model->ordenCompraProducto as $key => $_ocp) {
                        echo '<tr>';
                        echo $this->render('_form-productos-orden-compra', [
                        'key' => $_ocp->isNewRecord ? (strpos($key, 'new') !== false ? $key : 'new' . $key) : $_ocp->id,
                        'form' => $form,
                        'ocp' => $_ocp,
                        ]);
                        echo '</tr>';
                    }
                    
                    echo '<tr id="new-OCP-block" style="display: none;">';
                    echo $this->render('_form-productos-orden-compra', [
                    'key' => '__id__',
                    'form' => $form,
                    'ocp' => $OCP,
                    ]);

                    echo '</tr>';
                    echo '</tbody>';
                echo '</table>';
            echo '</div>';
        ?>
        </fieldset>
        </div>
    </div>

    <div class="form-group">
        <?php //echo Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ob_start(); // output buffer the javascript to register later ?>
        <script>
    //-------------------------- Scripts de Productos ----------------------------//
        // add OCP button
        var OCP_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
            $('#add-producto-btn').on('click', function () {
                OCP_k += 1;
                $('#productos').find('tbody')
                .append('<tr>' + $('#new-OCP-block').html().replace(/__id__/g, 'new' + OCP_k) + '</tr>');

                document.getElementById('saveBtn').disabled = false;  
            });

            // remove Producto button
            $(document).on('click', '.remove-OCP-btn', function () {
                $(this).closest('tbody tr').remove();

               if(document.getElementById('productos').rows.length <= 2) //Cuando la tabla no tiene registro tiene 2 filas, la cabecera y la fila oculta new-OCP-block
                    document.getElementById('saveBtn').disabled = true;
               else 
                    document.getElementById('saveBtn').disabled = false;  
            });

            <?php
                // OPTIONAL: click add when the form first loads to display the first new row
                if (!Yii::$app->request->isPost && $model->ordenCompra->isNewRecord) {
                    echo "$('#add-producto-btn').click();";
                }
            ?>

                var table,
                  $input,
                  hiddenId,
                  $Hinput;
               
               //Método de llenado de los datos de los productos
                fill_datatable();
                function fill_datatable(filter = '') {
                    table = $('#tproductos').DataTable({
                        "processing" : true,
                        "serverSide" : true,
                        //"bLengthChange" : false,
                        "order" : [],
                        "searching" : false,
                        "language" : {
                            "processing"	:  "Procesando...",
                            "lengthMenu"	: "Mostrar _MENU_ filas",
                            "info"		  : "Mostrando _START_ - _END_ de _TOTAL_ registros",
                            "infoEmpty"	 : "Mostrando 0 - 0 de 0 registros",
                            "infoFiltered"  : "(Filtrado de _MAX_ registros)",
                            "infoPostFix"   : "",
                            "loadingRecords": "Cargando...",
                            "zeroRecords"   : "No se encontraron resultados",
                            "emptyTable"	: "No hay datos",
                            "paginate" : {
                                "first"	 : "Primero",
                                "previous"  : "«",
                                "next"	  : "»",
                                "last"	  : "Último",
                            },
                        },
                        "ajax" : {
                            url:"<?= Url::toRoute('/inventario/productos/getajaxproducts'); ?>",
                            type:"POST",
                            data:{
                                filterVal:filter,
                                showCosto: true,
                                allExistences:'true'
                            }
                        },
                        "columnDefs": [
                            {
                                "targets": [ 2 ],
                                "visible": false,
                                "searchable": false
                            },
                        ]
                    });

                    //Para esto modifique la libreria datatables.min.js y agregue pageSize a un div que salia vacio para utilizarlo y y mostrar el componente de busqueda
                    $('.pageSize')[0].innerHTML = '<label>Buscar:</label><input type="search" id="product_search" class="form-control form-control-sm" value="'+filter+'" placeholder="Filtrar valor" aria-controls="tproductos">'

                    //Search products Table filter
                    $('#product_search').on('keyup', function () {
                        var f = $(this).val();
                        if(f != '') {
                            $('#tproductos').DataTable().destroy();
                            fill_datatable(f);
                        }
                        else {
                            $('#tproductos').DataTable().destroy();
                            fill_datatable();
                        }
                    });

                    setInputCursor($('#product_search')[0]);
                };

                //Search products Table filter
             /*   $('#product_search').on('keyup', function () {
                    var filterVal = $(this).val();
                    if(filterVal != '') {
                        $('#tproductos').DataTable().destroy();
                        fill_datatable(filterVal);
                    }
                    else {
                        $('#tproductos').DataTable().destroy();
                        fill_datatable();
                    }
                });*/

                //Mostrar dialogo de seleccion de productos
                $(document).on('click', '.prod_input', function () {
                    if($('#modal_table').hasClass('in') == true) {
                        $input = this;
                        $('#modal_launch').click();
                    }
                });
                $(document).on('focus', '.prod_input', function () {
                    if($('#modal_table').hasClass('in') == false) {
                        $input = this;
                        $('#modal_launch').click();
                    }
                });

                //Boton oculto para recargar lista de productos luego que se añade uno no existente
                $(document).on('click', '.tableProdReloader', function () {
                    $('#tproductos').DataTable().destroy();
                    fill_datatable();
                    console.log('table Productos List reloaded');
                    $('#success-msg').html("El producto fue insertado satisfactoriamente").fadeIn().delay(3000).fadeOut();
                });

                //Table Row selection Event
                $('#tproductos tbody').on('click', 'tr', function () {
                   $(this).toggleClass('selected');

                   $input.value = table.rows('.selected').data()[0][1];   //Nombre Producto
                   document.getElementById('codigo_'+$input.id).value = table.rows('.selected').data()[0][0];  //Codigo del producto
                   document.getElementById('id_'+$input.id).value = table.rows('.selected').data()[0][2];  //Id del producto
                   document.getElementById('costo_'+$input.id).value = table.rows('.selected').data()[0][4];  //Costo del producto
                   
                   $(this).toggleClass('selected');
                   $('#close_modal').click();
                });

                //Boton agregar nuevo producto no existente
                $(document).on('click', '.quick-add-product', function () {       
                 $('#NewProductWindow').modal('show').find('.modal-md').load('<?= Yii::$app->urlManager->createUrl("inventario/productos/create-via-ajax") ?>');
                });

        </script>
        <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>

    <?php ActiveForm::end(); ?>
</div>
