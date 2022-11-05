<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\MonedaCambio */

$this->title = 'Tasa de cambio';
$this->params['breadcrumbs'][] = ['label' => 'Moneda Cambios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="moneda-cambio-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p> 
        <?= Html::a('Cambiar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Nueva', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'m1_id',
                'value'=>$model->m1->nombre
            ],
            [
                'attribute'=>'m2_id',
                'value'=>$model->m2->nombre
            ],
            'valor',
        ],
    ]) ?>

</div>
