<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\MonedaCambio */

$this->title = 'Nueva Tasa Cambio';
$this->params['breadcrumbs'][] = ['label' => 'Moneda Cambios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moneda-cambio-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'monedas' => $monedas
    ]) ?>

</div>
