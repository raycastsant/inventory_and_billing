<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\MonedaCambioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tasas de cambio';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moneda-cambio-index">
<p>
    <h1><?= Html::encode($this->title) ?>
        <?= Html::a('Nueva', ['create'], ['class' => 'btn btn-success']) ?>
    </h1>
</p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'm1',
                'value'=>'m1.nombre',
                'label'=>'Moneda 1'
             ],
             [
                'attribute' => 'm2',
                'value'=>'m2.nombre',
                'label'=>'Moneda 2'
             ],
            'valor',

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
