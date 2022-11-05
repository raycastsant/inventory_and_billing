<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Inventario\models\Producto */

$this->title = 'Actualizar Producto: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Productos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="producto-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model, 'tipoproductos' => $tipoproductos, 'unidad_medidas' => $unidad_medidas, 'isUpdate'=>true
    ]) ?>

</div>
