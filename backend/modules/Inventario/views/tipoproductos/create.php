<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Tipoproducto */

$this->title = 'Nueva categoría de producto';
$this->params['breadcrumbs'][] = ['label' => 'Tipoproductos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipoproducto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model,
    ]) ?>

</div>
