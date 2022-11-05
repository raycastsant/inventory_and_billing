<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\CeoInfo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ceo Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ceo-info-view">

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
        <?= Html::a('Nuevo Ceo Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'ci',
            'cuenta_cuc',
            'cuenta_mn',
            'sucursal',
            'direccion',
            'telefono',
            'email:email',
            'actividad',
            'regime',
        ],
    ]) ?>

</div>
