<?php

use backend\modules\Facturacion\ESTADOS;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
//use backend\components\Helper;
//use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Producto */

$this->title = "PRODUCTO: ".$model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

//Modal que muestra el Zoom de la imagen
/*Modal::begin([
    'header' => '<h4>'.$model->codigo.', '.$model->nombre.'</h4>',
    'toggleButton' => ['label'=>'', 'id'=>'modal_launch', 'style'=>'display:none'],
    'closeButton'=>['id'=>'close_modal'],
    ]); 
        echo Html::img(Yii::$app->request->baseUrl.'//uploads//'.$model->nombre_imagen, 
            ['width' => 'auto', 'height' => 'auto']);     
    Modal::end();*/
?>
<div class="producto-view">
    <div class="row">
        <div class="col-md-5">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-7">
        <h1><?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Insertar nuevo', ['create'], ['class' => 'btn btn-success']) ?></h1>
        </div>
    </div>

    <div class="tabbable"> 
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Datos</a></li>
            <li><a href="#tab2" data-toggle="tab">Registro de operaciones</a></li>
            <?php 
                $serviciosProv = $model->getReservasServiciosQuery()->asArray()->all();
                if( count($serviciosProv) > 0 ) { ?>
                    <li><a href="#tab3" data-toggle="tab">Reservas-Órdenes-Servicios</a></li>
            <?php }  

                $ventasProv = $model->getReservasVentasQuery()->asArray()->all();
                if( count($ventasProv) > 0 ) { ?>
                    <li><a href="#tab4" data-toggle="tab">Reservas-Órdenes-Ventas</a></li>
            <?php }   ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                       'cant_reservada',
                        [   
                            'label'=>'Categoría',
                            'value'=>$model->tipoproducto->tipo
                        ],
                        [   
                            'label'=>'Unidad de medida',
                            'value'=>$model->unidadMedida->unidad_medida //$model->getUnidadMedida_Record()['unidad_medida']
                        ],
                        'nombre',
                        'costo',
                        'precio',
                        'codigo',
                        [
                            'attribute' => 'desc',
                            'format'=>'html',
                        ],
                        //'desc:ntext',
                        'existencia',
                        'stock_minimo',
                        'desc_ampliada:ntext',
                        [
                            'attribute' => 'nombre_imagen',
                            'label'=>'Imagen',
                            'format'=>'html',
                            'value' => function($data) {
                                if($data['nombre_imagen'])
                                return Html::img(Yii::$app->request->baseUrl.'//uploads//'.$data['nombre_imagen'], 
                                        ['width' => 'auto', 'height' => 'auto']);   
                                 /*   return Helper::ImgThumbailWidget(Yii::$app->request->baseUrl.'//uploads//'.$data['nombre_imagen'], 'zoomBtn', 
                                            $data['codigo'].', '.$data['nombre'], 'i'.$data['id'], "auto", "auto");*/
                                else
                                    return '';
                            },
                        ],
                    ],
                ]) ?>
            </div><!-- END Tab1 DIV -->

            <div class="tab-pane" id="tab2">
                <?= $this->render('_trazas', ['model'=>$model]); ?>
            </div><!-- END Tab2 DIV -->

            <?php //TAB de Ordenes de Servicios
            if(count($serviciosProv) > 0) {   ?>
                <div class="tab-pane" id="tab3">  
                    <div class="table-responsive">
                        <table id="treservas_servicios" class="display table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><a href="#">Orden</a></th>
                                <th><a href="#">Cantidad reservada</a></th>
                                <th></th> 
                            </tr>
                        </thead>
                    <?php 
                            foreach($serviciosProv as $row) {
                                echo "<tr>";
                                echo "<td>".$row['codigo']."</td>";
                                echo "<td>".$row['cant_productos']."</td>";
                                echo "<td>".Html::a('<span class="glyphicon glyphicon-search"></span>', ['/facturacion/ordenservicios/view', 'id'=>$row['id']], ['class' => 'btn btn-default btn-xs', 'target'=>'_blank'])."</td>";
                                echo "</tr>";
                            }  ?>   
                        </table>
                    </div>
                </div><!-- END Tab3 DIV -->
            <?php }   ?>

            <?php //TAB de Ordenes de Ventas
            if( count($ventasProv) > 0 ) {   ?>
                <div class="tab-pane" id="tab4">  
                    <div class="table-responsive">
                            <table id="treservas_ventas" class="display table table-striped table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th><a href="#">Orden</a></th>
                                    <th><a href="#">Cantidad reservada</a></th>
                                    <th></th> 
                                </tr>
                            </thead>
                        <?php 
                                foreach($ventasProv as $row) {
                                    //print_r($row->productosOrdenVentas);
                                    echo "<tr>";
                                    echo "<td>".$row['codigo']."</td>";
                                    echo "<td>".$row['cantidad']."</td>";
                                    echo "<td>".Html::a('<span class="glyphicon glyphicon-search"></span>', ['/facturacion/ordenventas/view', 'id'=>$row['id']], ['class' => 'btn btn-default btn-xs', 'target'=>'_blank'])."</td>";
                                    echo "</tr>";

                                    /*echo "<tr>";
                                    echo "<td>".$row->codigo."</td>";
                                    echo "<td>".$row->productosOrdenVentas[0]->cantidad."</td>";
                                    echo "<td>".Html::a('<span class="glyphicon glyphicon-search"></span>', ['/facturacion/ordenventas/view', 'id'=>$row->id], ['class' => 'btn btn-default btn-xs', 'target'=>'_blank'])."</td>";
                                    echo "</tr>";*/
                                }  ?>   
                            </table>
                        </div>
                </div><!-- END Tab4 DIV -->
            <?php }   ?>
        </div><!-- END Tab content DIV -->
    </div><!-- END Tababble DIV -->
</div>

<?php
ob_start(); // output buffer the javascript to register later ?>
<script>
    fill_servicios_table();
    function fill_servicios_table(filter = '') {
        table = $('#treservas_servicios').DataTable(getEsDatatableConfig());
    };
    fill_ventas_table();
    function fill_ventas_table(filter = '') {
        table = $('#treservas_ventas').DataTable(
            getEsDatatableConfig()
        );
    };
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
