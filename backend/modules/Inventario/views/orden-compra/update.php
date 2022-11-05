<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\inventario\models\OrdenCompra */

$this->title = 'Actualizar Orden Compra:'.$model->ordenCompra->id;
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->ordenCompra->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="orden-compra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
