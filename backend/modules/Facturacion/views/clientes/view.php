<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use backend\modules\Facturacion\ESTADOS;


/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Cliente */

$this->title = 'Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cliente-view">
<p>
    <h1><?= Html::encode($this->title) ?>
        <?= Html::a('Cambiar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Nuevo', ['create'], ['class' => 'btn btn-success']) ?>
    </h1>
</p>
    <div class="tabbable"> 
      <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Descripción</a></li>

            <li><a href="#tab2" data-toggle="tab">Vehículos</a></li>

            <?php 
                $ventasProv = $model->getVentasQuery()->all();
                if(count($ventasProv) > 0) { ?>
                    <li><a href="#tab3" data-toggle="tab">Órdenes-Ventas</a></li>
            <?php }   

                $serviciosProv = $model->getServiciosQuery()->all();
                if(count($serviciosProv) > 0) { ?>
                <li><a href="#tab4" data-toggle="tab">Órdenes-Servicios</a></li>
            <?php }   ?>

      </ul>
      <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'id',
                    [   
                        'label'=>'Tipo de cliente',
                        'value'=>$model->tipoCliente->nombre 
                    ],
                    'nombre',
                   /* [
                        'attribute'=>'codigo',
                        'label'=>'Código'
                    ],*/
                    [
                        'attribute'=>'telefono',
                        'label'=>'Teléfono'
                    ],
                    'fax',
                    'direccion',
                    'email',
                ],
            ]) ?>
            
            <?php
            /**Lista de responsables */
            if( ($model->tipoCliente->nombre == 'Empresa') && 
                count($model->clienteEmpresaResponsables) > 0) { ?>
                <legend>Responsables de la empresa</legend>
                <table class="table table-striped table-bordered">
                <tr><th>Nombre</th><th>Teléfono</th><th>CI</th><th>Email</th></tr>
            <?php   
                foreach($model->clienteEmpresaResponsables as $cer) { ?>
                    <tr>
                        <td><?= $cer->nombre ?> </td>
                        <td><?= $cer->telefono ?></td>
                        <td><?= $cer->ci ?></td>
                        <td><?= $cer->email ?></td>
                    </tr>
                <?php
                } ?>
            </table>
            <?php
            } ?>
        </div>
        <div class="tab-pane" id="tab2">
            
    <?php   /**Lista de vehiculos */
            if(count($model->vehiculos) > 0) { ?>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Chapa</th>
                        <th>Modelo</th>
                        <th>Marca</th>
                        <th>Fabricante</th>
                        <th>Año</th>
                        <th>Código-motor</th>
                        <th>Código-alternador</th>
                    </tr>
            <?php   
                foreach($model->vehiculos as $v) { ?>
                    <tr>
                        <td><?= $v->chapa ?> </td>
                        <td><?= $v->modelo ?></td>
                        <td><?= $v->marca ?></td>
                        <td><?= $v->fabricante ?></td>
                        <td><?= $v->anno ?></td>
                        <td><?= $v->codigo_motor ?></td>
                        <td><?= $v->codigo_alternador ?></td>
                    </tr>
            <?php
                } ?>
                </table>
    <?php }  ?>
        </div>
        <?php //TAB de Ordenes de Ventas
            if(count($ventasProv) > 0) {   ?>
                <div class="tab-pane" id="tab3">  
                    <div class="table-responsive">
                        <table id="tventas" class="display table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><a href="#">Código</a></th>
                                <th><a href="#">Estado</a></th>
                                <th></th> 
                            </tr>
                        </thead>
                    <?php 
                            foreach($ventasProv as $row) {
                                echo "<tr>";
                                echo "<td>".$row->codigo."</td>";
                                $e = $row->estadoOrden->estado;

                                if($e == ESTADOS::ABIERTO)
                                    echo '<td><span class="label label-info">'.$e.'</span></td>';
                                else
                                if($e == ESTADOS::CANCELADO)
                                    echo '<td><span class="label label-default">'.$e.'</span></td>';
                                else
                                if($e == ESTADOS::FACTURADO)
                                    echo '<td><span class="label label-warning">'.$e.'(por cobrar)</span></td>';
                                else
                                if($e == ESTADOS::COBRADO)
                                    echo '<td><span class="label label-success">'.$e.'</span></td>';

                                echo "<td>".Html::a('<span class="glyphicon glyphicon-search"></span>', ['/facturacion/ordenventas/view', 'id'=>$row->id], ['class' => 'btn btn-default btn-xs', 'target'=>'_blank'])."</td>";
                                echo "</tr>";
                            }  ?>   
                        </table>
                    </div>
                </div>
        <?php }   ?>

        <?php //TAB de Ordenes de Servicios
            if(count($serviciosProv) > 0) {   ?>
                <div class="tab-pane" id="tab4">  
                    <div class="table-responsive">
                        <table id="tservicios" class="display table table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><a href="#">Código</a></th>
                                <th><a href="#">Estado</a></th>
                                <th></th> 
                            </tr>
                        </thead>
                    <?php 
                            foreach($serviciosProv as $row) {
                                echo "<tr>";
                                echo "<td>".$row->codigo."</td>";
                                $e = $row->estadoOrden->estado;

                                if($e == ESTADOS::ABIERTO)
                                    echo '<td><span class="label label-info">'.$e.'</span></td>';
                                else
                                if($e == ESTADOS::CANCELADO)
                                    echo '<td><span class="label label-default">'.$e.'</span></td>';
                                else
                                if($e == ESTADOS::FACTURADO)
                                    echo '<td><span class="label label-warning">'.$e.'(por cobrar)</span></td>';
                                else
                                if($e == ESTADOS::COBRADO)
                                    echo '<td><span class="label label-success">'.$e.'</span></td>';

                                echo "<td>".Html::a('<span class="glyphicon glyphicon-search"></span>', ['/facturacion/ordenservicios/view', 'id'=>$row->id], ['class' => 'btn btn-default btn-xs', 'target'=>'_blank'])."</td>";
                                echo "</tr>";
                            }  ?>   
                        </table>
                    </div>
                </div>
        <?php }   ?>
      </div>
    </div>
</div>

<?php
ob_start(); // output buffer the javascript to register later ?>
<script>
    fill_servicios_table();
    function fill_servicios_table(filter = '') {
        table = $('#tservicios').DataTable(getEsDatatableConfig());
    };
    fill_ventas_table();
    function fill_ventas_table(filter = '') {
        table = $('#tventas').DataTable(
            getEsDatatableConfig()
        );
    };
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>
