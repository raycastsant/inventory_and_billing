<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\inventario\models\OrdenCompra */

$this->title = 'Nueva Orden de Compra';
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-compra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'serie' => $serie
    ]) ?>

</div>
