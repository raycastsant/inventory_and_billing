<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\ClienteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clientes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cliente-index">
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
            {items} {pager} ',
        'filterSelector' => '#myPageSize',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
          //  'id',
           /* [
                'attribute' => 'codigo',
                'label' => 'Código'
            ],*/
            [
                'attribute' => 'nombre',
                'contentOptions' => ['style' => 'max-width:150px; white-space: pre-wrap;'],
            ],
            [
                'attribute' => 'tipoCliente',
                'label'=>'Tipo de cliente',
                'value'=>'tipoCliente.nombre'
             ],
             [
                'attribute' => 'telefono',
                'contentOptions' => ['style' => 'max-width:80px; white-space: pre-wrap;'],
            ],
            'email',
            [
                'attribute' => 'direccion',
                'contentOptions' => ['style' => 'max-width:600px; white-space: pre-wrap;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}', 
                'buttons' => [
                    'view' => function($url, $model) {
                        $options = [
                            'title' => 'Ver',
                            'aria-label' => 'Ver',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-search"></span>', $url, $options);
                    },
                    'update' => function($url, $model) {
                        $options = [
                            'title' => 'Editar',
                            'aria-label' => 'Editar',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                    'delete' => function($url, $model) {
                        $options = [
                            'title' => 'Eliminar',
                            'aria-label' => 'Eliminar',
                            'data-confirm' => '¿Está seguro que desea eliminar el cliente?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <?php 
    echo Yii::$app->view->renderFile(Yii::getAlias('@app').'/views/_FilterFocusScript.php');
    
    /*    $this->registerJs('
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
        });');*/
     ?>
</div>
