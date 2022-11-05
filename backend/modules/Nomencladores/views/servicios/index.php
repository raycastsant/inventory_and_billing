<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Nomencladores\models\ServicioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicio-index">
<legend>
    <div class="row">
        <div class="col-md-2">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-10">
            <h1><?= Html::a('Nuevo', ['create'], ['class' => 'btn btn-success']) ?></h1>
        </div>
    </div>
</legend>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>'<div class="row">
                <div class="col-md-1 pageSizeLabel"><label>Cantidad de filas</label></div>
                <div class="col-md-1 pageSizeSelector">'.
                    Html::activeDropDownList($searchModel, 'myPageSize', 
                    [10 => 10, 20 => 20, 50 => 50, 100 => 100, 500=>500],
                    ['id'=>'myPageSize']).' </div> 
                <div class="col-md-10" style="width:600px"> {summary} </div>
                </div>
            {items} {pager}',
        'filterSelector' => '#myPageSize',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
          //  'id',
            'nombre',
            [
                'attribute' => 'descripcion',
                'label'=>'Descripción',
                'value'=>'descripcion'
             ],
           // 'eliminado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
    
    <?php 
        echo Yii::$app->view->renderFile(Yii::getAlias('@app').'/views/_FilterFocusScript.php');
     ?>
</div>
