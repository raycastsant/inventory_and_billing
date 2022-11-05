<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\OrdenVenta */

$this->title = 'Actualizar Orden Venta: ' . $model->ordenVenta->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Orden Ventas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ordenVenta->id, 'url' => ['view', 'id' => $model->ordenVenta->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="orden-venta-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,  'clientes' => $clientes, 'estado_id'=>$model->ordenVenta->estadoOrden->id, 
        'area_id'=>$model->ordenVenta->area->id, 'vehiculos'=>$vehiculos, 'areas'=>$areas
    ]) ?>

</div>
