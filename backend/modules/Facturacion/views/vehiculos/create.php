<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Vehiculo */

$this->title = 'Nuevo Vehículo';
$this->params['breadcrumbs'][] = ['label' => 'Vehiculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehiculo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'clientes' => $clientes, 'annos' => $annos,
    ]) ?>

</div>
