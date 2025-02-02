<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Cliente */

$this->title = 'Nuevo Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cliente-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'tipoclientes' => $tipoclientes, 'annos' => $annos,
    ]) ?>

</div>
