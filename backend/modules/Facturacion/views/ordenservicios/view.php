<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\modules\Facturacion\ESTADOS;
use backend\components\UserRole;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\OrdenServicio */

$this->title = "Orden de servicios - ".$model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Orden Servicios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<!-- Dialogo de editar vehiculo y cliente-->
<div class="modal inmodal" id="updateVehicleDialog" role="dialog" data-keyboard="false">
    <div class="modal-dialog modal-md" style="min-width:300px">
        <div class="modal-content animated bounceInTop">
            <?php $form = ActiveForm::begin(['id'=>'edit-vehicle-form',]);   ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-left">Actualizar información de la orden</h4>
            </div>
            <div class="modal-body well">       
                <?php
                    $model->cliente = $model->vehiculo->cliente;
                    echo $form->field($model, 'cliente')->widget(Select2::classname(), [
                    'data' => $clientes,
                    'language' => 'es',
                    'options' => ['placeholder' => '-Seleccionar Cliente-', 
                                    'onchange'=>'
                                    $.getJSON("'.Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id=').'"+$(this).val(), function (jsondata) {
                                        document.getElementById("ordenservicio-vehiculo_id").innerHTML = jsondata;
                                    });
                                   /* $.post( "'.Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id=').'"+$(this).val(), function( data ) {
                                        document.getElementById("ordenservicio-vehiculo_id").innerHTML = data;
                                    });*/
                                    ' ],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ])->label('Cliente');  ?>
                <?= $form->field($model, 'vehiculo_id')->widget(Select2::classname(), [
                    'data' =>  Array(),
                    'language' => 'es',
                    'options' => ['placeholder' => '-Seleccionar Vehículo-'],
                    'pluginOptions' => [
                        'allowClear' => false
                    ], ])->label('Vehículo'); ?>
            </div>
            <br>
            <div class="form-group">
                <button type="button" id="okBtn" class="btn btn-success">Aceptar</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div> 

<div class="orden-servicio-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a('Nueva orden', ['create'], ['class' => 'btn btn-success']) ?>
    <?php 
    //Para verificar que el usuario sea jefe de area para imprimir
    $user_id = Yii::$app->user->getId();
    $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
    $firma = true;  //in_array(UserRole::ROL_JEFE_AREA, $keys);

    $estado = $model->estadoOrden->estado;
    if($estado != ESTADOS::CANCELADO) {
        //Menu Imprimir Oferta 
        echo $this->render("_view-menus/_menu-imprimir-oferta.php", ['model' => $model, 'firma' => $firma]);

        if($estado == ESTADOS::ABIERTO) {
            echo Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right']);
        }
        else { 
             //Menu Imprimir Factura
             echo $this->render("_view-menus/_menu-imprimir-factura.php", ['model' => $model, 'firma' => $firma]);
        }

        //Menu cambiar estado
       echo $this->render("_view-menus/_menu-cambiar-estado.php", ['estado' => $estado, 'model' => $model]);

       //Menu devoluciones   
       if( ($estado==ESTADOS::COBRADO || $estado==ESTADOS::FACTURADO) && count($model->productosOrdenServicios)>0 ) { 
           echo $this->render("_view-menus/_menu-devoluciones.php", ['estado' => $estado, 'model' => $model]);
       }
    } 
?>
    <div class="tabbable"> 
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Datos</a></li>
            <li><a href="#tab2" data-toggle="tab">Registro de operaciones</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'id',
                        [
                            'captionOptions' => ['style' => 'width:200px'],
                            'attribute'=>'codigo',
                            'label'=>'Código',
                        ],
                        [
                            'attribute'=>'cliente',
                            'label'=>'Cliente',
                            'format'=>'raw',
                            'value'=>$model->vehiculo->cliente->nombre/*.'      '.
                                        '<a href="javascript:void(0);" id="btn_edit_cliente" class="btn btn-default btn_edit_vehiculo"><span class="glyphicon glyphicon-pencil"></span></a>'*/
                        ],
                        [
                            'attribute'=>'vehiculo',
                            'label'=>'Vehículo',
                            'format'=>'raw',
                            'value'=>'<b>Modelo:</b>  '.$model->vehiculo->modelo.', <b>Marca:</b>  '.$model->vehiculo->marca.', <b>Chapa:</b>  '.$model->vehiculo->chapa.'     '.
                                        '<a href="#" class="btn btn-default btn_edit_vehiculo"><span class="glyphicon glyphicon-pencil"></span></a>',
                        ],
                        [
                            'attribute'=>'estado_orden_id',
                            'label'=>'Estado',
                            'format'=>'raw',
                            'value'=> function($model) {
                                $e = $model->estadoOrden->estado;

                                if($e == ESTADOS::ABIERTO)
                                    return Html::decode(Html::decode('<span class="label label-info">'.$e.'</span>'));
                                else
                                if($e == ESTADOS::CANCELADO)
                                    return '<span class="label label-default">'.$e.'</span>';
                                else
                                if($e == ESTADOS::FACTURADO)
                                    return '<span class="label label-warning">'.$e.'(por cobrar)</span>';
                                else
                                if($e == ESTADOS::COBRADO)
                                    return '<span class="label label-success">'.$e.'</span>';

                            }
                            //'value'=>$model->estadoOrden->estado
                        ],
                /*     [
                            'attribute'=>'user_id',
                            'label'=>'Autor',
                            'value'=>$model->user->username
                        ],*/
                        'fecha_iniciada',
                        'fecha_facturada',
                    /*  [
                            'attribute'=>'precio_estimado',
                            'value'=>'$ '.$model->precio_estimado
                        ]*/
                    ],
                ]);

                Yii::$app->session->set('montoProd', 0);
                Yii::$app->session->set('montoServ', 0);
                Yii::$app->session->set('beneficioProd', 0);
                Yii::$app->session->set('beneficioServ', 0);

                /**Lista de productos */ 
                    echo $this->render('_lista-productos', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'cambioCosto' => $cambioCosto,]);
                    $montoProd =  Yii::$app->session->get('montoProd');
                    $beneficioProd =  Yii::$app->session->get('beneficioProd');
                
                /**Lista de servicios */ 
                    echo $this->render('_lista-servicios', ['model' => $model, 'cambio' => $cambio, 'moneda_salida' => $moneda_salida, 'cambioCosto' => $cambioCosto,]);
                    $montoServ =  Yii::$app->session->get('montoServ');
                    $beneficioServ =  Yii::$app->session->get('beneficioServ');
                  ?>
                  
                    <table class="table table-striped table-bordered">
                        <tr style="background-color: #ddd;">
                            <th style="text-align:right;"><span class="subtotal">TOTAL</span></th>
                            <th style="text-align:right; width:150px;"><span class="subtotal"><?= round(($beneficioProd+$beneficioServ), 2 ).' '.$moneda_salida ?></span></th>
                            <th style="text-align:right; width:150px;"><span class="subtotal"><?= round(($montoServ+$montoProd), 2 ).' '.$moneda_salida ?></span></th>
                        </tr>
                    </table>
            </div><!-- END Tab1 DIV -->

            <div class="tab-pane" id="tab2">
                <?= $this->render('_trazas', ['model'=>$model]); ?>
            </div><!-- END Tab2 DIV -->
        </div><!-- END Tab content DIV -->
    </div><!-- END Tababble DIV -->
</div>

<?php ob_start(); // output buffer the javascript to register later ?>
    <script>
        //Boton editar vehiculo y cliente
        $(document).on('click', '.btn_edit_vehiculo', function () {  
            $.getJSON("<?= Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id='.$model->vehiculo->cliente->id) ?>", function (jsondata) {
                document.getElementById("ordenservicio-vehiculo_id").innerHTML = jsondata;
            });

           /* $.post("<?php //echo Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id='.$model->vehiculo->cliente->id); ?> ", function( data ) {
                                        document.getElementById("ordenservicio-vehiculo_id").innerHTML = data;  }); */  

            $('#updateVehicleDialog').modal('show');
        });

        $('#okBtn').on('click', function () {
            var cliente = $('#ordenservicio-cliente').val();
            var vehiculo = $('#ordenservicio-vehiculo_id').val();

            if(cliente!=null && vehiculo!=null && cliente.trim().length>0 && vehiculo.trim().length>0) {
                $('#edit-vehicle-form').submit();
            }
            else{
                if(cliente==null || cliente.trim().length <= 0) {
                   $('#ordenservicio-cliente').closest('.field-ordenservicio-cliente')[0].classList.add("has-error");
                   $('#ordenservicio-cliente').closest('.field-ordenservicio-cliente')[0].children[3].innerHTML = "Seleccione el cliente";
                }  

                if(vehiculo==null || vehiculo.trim().length <= 0) {
                    $('#ordenservicio-vehiculo_id').closest('.field-ordenservicio-vehiculo_id')[0].classList.add("has-error");
                    $('#ordenservicio-vehiculo_id').closest('.field-ordenservicio-vehiculo_id')[0].children[3].innerHTML = "Seleccione el vehículo";
                }
            }
        });
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
