<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\modules\Facturacion\ESTADOS;


$this->title = 'Órdenes de Ventas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-venta-index">
    <legend>
        <div class="row">
            <div class="col-md-4">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="col-md-8">
                <h1><?= Html::a('Nueva orden', ['create'], ['class' => 'btn btn-success']) ?></h1>
            </div>
        </div>
    </legend>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '<div class="row">
                <div class="col-md-1 pageSizeLabel"><label>Cantidad de filas</label></div>
                <div class="col-md-1 pageSizeSelector">' .
            Html::activeDropDownList(
                $searchModel,
                'myPageSize',
                [10 => 10, 20 => 20, 50 => 50, 100 => 100, 500 => 500],
                ['id' => 'myPageSize']
            ) . ' </div> 
                <div class="col-md-10" style="width:600px"> {summary} </div>
                </div>
            {items} {pager} ',
        'filterSelector' => '#myPageSize',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'area',
                'label' => 'Área',
                'value' => 'area.nombre'
            ],
            [
                'attribute' => 'codigo',
                'label' => 'Código'
            ],
            [
                'attribute' => 'cliente',
                'label' => 'Cliente',
                'value' => 'cliente.nombre'
            ],
            [
                'attribute' => 'estadoOrden',
                'value' => function ($model) {
                    $e = $model->estadoOrden->estado;

                    if ($e == ESTADOS::ABIERTO)
                        return '<span class="label label-info">' . $e . '</span>';
                    else
                    if ($e == ESTADOS::CANCELADO)
                        return '<span class="label label-default">' . $e . '</span>';
                    else
                    if ($e == ESTADOS::FACTURADO)
                        return '<span class="label label-warning">' . $e . '(por cobrar)</span>';
                    else
                    if ($e == ESTADOS::COBRADO)
                        return '<span class="label label-success">' . $e . '</span>';
                },  //'estadoOrden.estado',
                'filter' => [
                    'ABIERTA' => 'ABIERTA',
                    'FACTURADO' => 'FACTURADA',
                    'COBRADO' => 'COBRADA',
                    'CANCELADO' => 'CANCELADA',
                ],
                'label' => 'Estado',
            ],
            /*  [
                'attribute' => 'user',
                'label'=>'Autor',
                'value'=>'user.username'
            ],*/
            'fecha_iniciada',
            [
                'attribute' => 'precio_estimado',
                'format' => 'Currency',
                'label' => 'Monto',
            ],
            [
                'attribute' => 'moneda',
                'label' => 'Moneda',
                'value' => function ($model) {
                    return $model->moneda->nombre;
                },
                'filter' => [1 => 'CUC', 2 => 'CUP'],
            ],
            //'fecha_cerrada',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $options = [
                            'title' => 'Ver',
                            'aria-label' => 'Ver',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-search"></span>', $url, $options);
                    },
                    'update' => function ($url, $model) {
                        if ($model->estadoOrden->estado != ESTADOS::ABIERTO) {
                            return '';
                        }
                        $options = [
                            'title' => 'Editar',
                            'aria-label' => 'Editar',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default'
                        ];
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end();
    echo Yii::$app->view->renderFile(Yii::getAlias('@app') . '/views/_FilterFocusScript.php');
    ?>
</div>