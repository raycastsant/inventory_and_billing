<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Tipoproducto */

$this->title = 'Atualizar categoría de producto';
$this->params['breadcrumbs'][] = ['label' => 'Tipoproductos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tipoproducto-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model]) ?>

</div>
