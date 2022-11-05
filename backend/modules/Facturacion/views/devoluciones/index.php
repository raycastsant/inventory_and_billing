<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\DevolucionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Devoluciones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="devolucion-index">
<legend>
    <div class="row">
        <div class="col-md-3">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>
</legend>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'ordenId',
                'format'=>'raw',
                'value'=> function($model) {
                    return $model->getOrden()->codigo;
                },  
                'label'=>'Orden',
            ],
            [
                'attribute' => 'parcial',
                'value'=> function($model) {
                    if($model->parcial == true)
                        return 'PARCIAL';
                    else
                        return 'TOTAL';

                },  
                'filter' => [
                    1 => 'PARCIAL',
                    0 => 'TOTAL',
                ],
                'label' => 'Tipo de devolución',
            ],
            [
                'attribute' => 'fecha',
                'value'=> function($model) {
                    return date('Y/m/d', strtotime($model->fecha));
                },  
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}', 
                'buttons' => [
                    'view' => function($url, $model) {
                        $options = [
                            'title' => 'Ver',
                            'aria-label' => 'Ver',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-search"></span>', 
                                    Url::toRoute('/facturacion/devoluciones/view').'?id='.$model->id, 
                                    $options);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <?= Yii::$app->view->renderFile(Yii::getAlias('@app').'/views/_FilterFocusScript.php');  ?>
</div>
