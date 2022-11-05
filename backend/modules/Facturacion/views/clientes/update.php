<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Cliente */

$this->title = 'Actualizar Cliente: ' . $model->cliente->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cliente->id, 'url' => ['view', 'id' => $model->cliente->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="cliente-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'tipoclientes' => $tipoclientes, 'annos' => $annos,
    ]) ?>

</div>
