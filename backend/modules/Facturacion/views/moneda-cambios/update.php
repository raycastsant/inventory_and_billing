<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\MonedaCambio */

$this->title = 'Actualizar Moneda Cambio: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Moneda Cambios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="moneda-cambio-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'monedas' => $monedas
    ]) ?>

</div>
