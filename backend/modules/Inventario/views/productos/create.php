<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Producto */

$this->title = 'Nuevo Producto';
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="producto-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model, 'tipoproductos' => $tipoproductos, 'unidad_medidas' => $unidad_medidas, 'isUpdate'=>false
    ]) ?>

</div>
