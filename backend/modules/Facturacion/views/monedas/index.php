<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\MonedaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monedas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moneda-index">
<p>
    <h1><?= Html::encode($this->title) ?>
        <?= Html::a('Nueva Moneda', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cambios', ['moneda-cambios/index'], ['class' => 'btn btn-default']) ?>
    </h1>   
</p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nombre',

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
