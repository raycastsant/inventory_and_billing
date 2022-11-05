<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Nomencladores\models\UnidadmedidaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unidades de medida';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unidad-medida-index">
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
           // 'id',
            'unidad_medida',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <?php 
        $this->registerJs('
            var inputName = null;
            jQuery("#p0").on("keyup", "input", function() {
                jQuery(this).change();
                inputName = this.name;
            });
        ',
        yii\web\View::POS_READY);

          //Cuando PJAX recargue establecer el cursor en la busqueda     
          $this->registerJs('jQuery(document).on("pjax:success", "#p0", function(event) {
            var el = $("input[name=\'"+inputName+"\']")[0];

            setInputCursor(el);
            inputName = null;
        });');
     ?>
</div>
