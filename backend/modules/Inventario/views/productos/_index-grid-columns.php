<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\Helper;

//use xj\hoverzoom\HoverzoomAsset;

  //  HoverzoomAsset::register($this);
  
 /* echo \hoomanMirghasemi\iviewer\Iviewer::widget([
      'selector' => '#iviewer-content',
      'loadingSelector'=>'#iv-loading',
      'beforeIviewer'=>'$("#iviewer-content").html("");',
      'imageSrc' => 'path to your image',
  ]);*/
  //echo yii\jui\DatePicker::widget(['name' => 'attributeName']) ;
  ?>

  <?php
    echo GridView::widget([
            'dataProvider' => $dataProvider,
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
                [
                    'attribute' => 'nombre_imagen',
                    'label'=>'Imagen',
                    //'contentOptions' => ['style' => 'max-width:100px;'],
                    'value' => function($data) {
                        if($data['nombre_imagen']) {
                            $h = @fopen(Url::base(true).'//uploads//'.$data['nombre_imagen'], 'r');
                            if($h)
                                return Helper::ImgThumbailWidget(Yii::$app->request->baseUrl.'//uploads//'.$data['nombre_imagen'], $data['id'], $data['codigo'].', '.$data['nombre'], 'i'.$data['id'], "70px", "70px");
                        }
                        else
                            return '';
                    },
                ],
                /* [
                    'attribute' => 'tipoproducto',
                    'label'=>'Tipo',
                    'value'=>'tipoproducto.tipo'
                 ],*/
                 [
                    'attribute' => 'codigo',
                    'label'=>'Código',
                 ],
                 [
                    'attribute' => 'nombre',
                    'contentOptions' => ['style' => 'max-width:200px; white-space: pre-wrap;'],
                 ],
               /*  [
                    'attribute' => 'costo',
                    'format'=>'Currency',
                 ],
                 [
                    'attribute' => 'precio',
                    'format'=>'Currency',
                 ],*/
                 [
                    'attribute' => 'desc',
                    //'label'=>'Descripción',
                    'contentOptions' => ['style' => 'max-width:300px; white-space: pre-wrap;'],
                    //'value' => Html::encode('desc')
                 ],
                 [
                    'attribute' => 'desc_ampliada',
                    'contentOptions' => ['style' => 'max-width:500px; white-space: pre-wrap;'],
                 ],
                'existencia',
               // 'cant_reservada',
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
                                'data-confirm' => '¿Está seguro que desea eliminar el Producto?',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                                'class' => 'btn btn-default'
                            ];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                        },
                    ]
                ],
            ],
        ]);

       