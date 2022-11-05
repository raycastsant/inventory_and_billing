<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\modules\Facturacion\ESTADOS;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;


$this->title = "Orden de ventas - " . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Orden Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<!-- Dialogo de editar cliente-->
<div class="modal inmodal" id="updateCVDialog" role="dialog" data-keyboard="false">
    <div class="modal-dialog modal-md" style="min-width:300px">
        <div class="modal-content animated bounceInTop">
            <?php $form = ActiveForm::begin(['id' => 'edit-cv-form',]);   ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-left">Actualizar cliente</h4>
            </div>
            <div class="modal-body well">
                <?= $form->field($model, 'cliente')->widget(Select2::class, [
                    'data' => $clientes,
                    'language' => 'es',
                    'options' => [
                        'placeholder' => '-Seleccionar Cliente-',
                    ],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ])->label('Cliente');  ?>
            </div>
            <br>
            <div class="form-group">
                <button type="button" id="okBtnCliente" class="btn btn-success">Aceptar</button>
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
    $firma = true; //in_array(UserRole::ROL_JEFE_AREA, $keys);

    $estado = $model->estadoOrden->estado;
    if ($estado != ESTADOS::CANCELADO) {
        //Menu Imprimir Oferta 
        echo $this->render("_view-menus/_menu-imprimir-oferta.php", ['model' => $model, 'firma' => $firma]);

        //Boton editar
        if ($estado == ESTADOS::ABIERTO) {
            echo Html::a('<span class="glyphicon glyphicon-pencil"></span> Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right']);
        } else {
            //Menu Imprimir Factura
            echo $this->render("_view-menus/_menu-imprimir-factura.php", ['model' => $model, 'firma' => $firma]);
        }

        //Menu cambiar estado
        echo $this->render("_view-menus/_menu-cambiar-estado.php", ['estado' => $estado, 'model' => $model]);

        //Menu devoluciones
        if (($estado == ESTADOS::COBRADO || $estado == ESTADOS::FACTURADO) && count($model->productosOrdenVentas) > 0) {
            echo $this->render("_view-menus/_menu-devoluciones.php", ['estado' => $estado, 'model' => $model]);
        }
    } ?>

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
                        [
                            'captionOptions' => ['style' => 'width:200px'],
                            'attribute' => 'codigo',
                            'label' => 'Código',
                        ],
                        [
                            'attribute' => 'cliente',
                            'label' => 'Cliente',
                            'format' => 'raw',
                            'value' => $model->cliente->nombre . '     ' .
                                '<a href="#" class="btn btn-default btn_edit_cliente"><span class="glyphicon glyphicon-pencil"></span></a>',
                        ],
                        [
                            'attribute' => 'estado_orden_id',
                            'label' => 'Estado',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $e = $model->estadoOrden->estado;

                                if ($e == ESTADOS::ABIERTO)
                                    return Html::decode(Html::decode('<span class="label label-info">' . $e . '</span>'));
                                else
                                if ($e == ESTADOS::CANCELADO)
                                    return '<span class="label label-default">' . $e . '</span>';
                                else
                                if ($e == ESTADOS::FACTURADO)
                                    return '<span class="label label-warning">' . $e . '(por cobrar)</span>';
                                else
                                if ($e == ESTADOS::COBRADO)
                                    return '<span class="label label-success">' . $e . '</span>';
                            }
                        ],
                        /* [
                            'attribute'=>'user_id',
                            'label'=>'Autor',
                            'value'=>$model->user->username
                        ],*/
                        'fecha_iniciada',
                        'fecha_facturada',
                    ],
                ]);

                /**Lista de productos */
                echo $this->render(
                    '_lista-productos',
                    [
                        'model' => $model,
                        'cambio' => $cambio,
                        'moneda_salida' => $moneda_salida,
                        'cambioCosto' => $cambioCosto,
                    ]
                );
                ?>
            </div><!-- END Tab1 DIV -->

            <div class="tab-pane" id="tab2">
                <?= $this->render('_trazas', ['model' => $model]); ?>
            </div><!-- END Tab2 DIV -->
        </div><!-- END Tab content DIV -->
    </div><!-- END Tababble DIV -->
</div>

<?php ob_start(); // output buffer the javascript to register later 
?>
<script>
    //Boton editar cliente 
    // $(document).on('click', '.btn_edit_cliente', function() {
    //     $.getJSON("<?= Yii::$app->urlManager->createUrl('facturacion/vehiculos/ajaxlist?id=' . $model->cliente->id) ?>", function(jsondata) {
    //         document.getElementById("ordenventa-vehiculo_id").innerHTML = jsondata;
    //     });

    //     $('#updateCVDialog').modal('show');
    // });

    $('#okBtnCliente').on('click', function() {
        var cliente = $('#ordenventa-cliente').val();

        if (cliente != null && cliente.trim().length > 0) {
            $('#edit-cv-form').submit();
        } else {
            if (cliente == null || cliente.trim().length <= 0) {
                $('#ordenservicio-cliente').closest('.field-ordenventa-cliente')[0].classList.add("has-error");
                $('#ordenventa-cliente').closest('.field-ordenventa-cliente')[0].children[3].innerHTML = "Seleccione el cliente";
            }
        }
    });
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>