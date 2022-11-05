<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Inventario\models\TipoproductoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categorías de productos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipoproducto-index">
<legend>
    <div class="row">
        <div class="col-md-5">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-7">
            <h1><?= Html::a('Nueva', ['create'], ['class' => 'btn btn-success']) ?></h1>
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
            //'id',
            'tipo',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); 
     
     echo Yii::$app->view->renderFile(Yii::getAlias('@app').'/views/_FilterFocusScript.php');

   /* $this->registerJs("
            $(document).on('click', '.kv-file-zoom', function () {
               var id = $(this)[0].id;
               if(id != null) {
                    $('#zoomHeader')[0].innerHTML = $('#i'+id)[0].title;
                    $('#zoom_img')[0].src = $('#i'+id)[0].src;
                    $('#modal_launch').click();
               }
            });
               ",  yii\web\View::POS_READY);*/
?>
</div>


