<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\OrdenServicio */

$this->title = 'Actualizar Orden Servicio: ' . $model->ordenServicio->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Listar órdenes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ordenServicio->id, 'url' => ['view', 'id' => $model->ordenServicio->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="orden-servicio-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,  'clientes' => $clientes, 'estado_id' => $model->ordenServicio->estadoOrden->id,
        'area_id' => $model->ordenServicio->area->id, 'servicios' => $servicios, 'trabajadores' => $trabajadores,
        'areas' => $areas   //'isCreate' => false
    ]) ?>

</div>