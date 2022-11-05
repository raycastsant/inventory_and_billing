<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Devolucion */

if($model->is_venta)
    $this->title = "Devolución ventas";
else 
    $this->title = "Devolución servicios";

\yii\web\YiiAsset::register($this);
?>
<div class="devolucion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php 
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'parcial',
                'value'=> function($model) {
                    if($model->parcial == true)
                        return 'PARCIAL';
                    else
                        return 'TOTAL';

                },  
                'label' => 'Tipo de devolución',
            ],
            [
                'attribute'=>'fecha',
                'value'=> function($model) {
                    return date('d/m/Y', strtotime($model->fecha));
                },  
            ],
            [
                'attribute'=>'ordenId',
                'format'=>'raw',
                'value'=> function($model) {
                    if($model->is_venta == true)
                        return '<a href="'.Url::toRoute('/facturacion/ordenventas/view').'?id='.$model->ordenId.'">'.$model->getOrden()->codigo.'</a>';
                    else
                        return '<a href="'.Url::toRoute('/facturacion/ordenservicios/view').'?id='.$model->ordenId.'">'.$model->getOrden()->codigo.'</a>';

                },  
                'label'=>'Orden',
            ]
        ],
    ]);

    if( $model->is_venta && count($model->listDevolucionVentas())>0 ) {
        echo $this->render("_view-products-list", ["list"=>$model->listDevolucionVentas()]);
    }
    else
    if( !$model->is_venta && count($model->listDevolucionServicios())>0 ) {
        echo $this->render("_view-products-list", ["list"=>$model->listDevolucionServicios()]);
    }
?>
</div>
