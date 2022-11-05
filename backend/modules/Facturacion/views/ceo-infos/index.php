<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\CeoInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ceo Infos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ceo-info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Nuevo Ceo Info', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nombre',
            'ci',
            'cuenta_cuc',
            'cuenta_mn',
            //'sucursal',
            //'direccion',
            //'telefono',
            //'email:email',
            //'actividad',
            //'regime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
