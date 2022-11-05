<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Vehiculo */

$this->title = 'Actualizar Vehículo';
$this->params['breadcrumbs'][] = ['label' => 'Vehiculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="vehiculo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'clientes' => $clientes, 'annos' => $annos,
    ]) ?>

</div>
