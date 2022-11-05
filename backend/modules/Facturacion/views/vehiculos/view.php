<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Vehiculo */

$this->title = 'Datos del vehículo';
$this->params['breadcrumbs'][] = ['label' => 'Vehiculos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="vehiculo-view">
<p> 
    <h1><?= Html::encode($this->title) ?>
    
        <?= Html::a('Cambiar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea eliminar el registro?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Nuevo', ['create'], ['class' => 'btn btn-success']) ?>
        </h1>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            [
                'attribute' => 'cliente',
                'label'=>'Cliente',
                'value'=>$model->cliente->nombre
             ],
            'chapa',
            'modelo',
            'marca',
            'fabricante',
            'anno',
            'codigo_motor',
            'codigo_alternador',
        ],
    ]) ?>

</div>
