<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\components\Helper;
use yii\bootstrap\Modal;


//Modal que muestra el Zoom de la imagen
Modal::begin([
    'header' => '<h4 id="zoomHeader"></h4>',
    'toggleButton' => ['label' => '', 'id' => 'modal_launch', 'style' => 'display:none'],
    'id' => 'modal_img',
    'closeButton' => ['id' => 'close_modal'],
]);
echo '<img id="zoom_img" class="file-preview-image kv-preview-data file-zoom-detail" 
                src="" alt="" 
                style="width: auto; height: auto; max-width: 100%; max-height: 100%;">';
Modal::end();

$this->title = 'Productos más vendidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prod-mas-vendidos-index">
    <div class="row">
        <div class="col-md-8">
            <legend><?= Html::encode($this->title) ?></legend>
        </div>
        <div class="col-md-4" id="reset_div">
            <h3><button id="resetBtn" class="btn">Reiniciar</button></h3>
        </div>
    </div>

    <?php
    Pjax::begin();
    /**Si no se establece la opcion 'type', se asume por defecto que el valor de la clave 
     * corresponde al LABEL del Input*/
    $fields = [
        'cantVenta' => 'Ventas a partir de:', 'codigo' => 'Código de producto', 'nombre' => 'Nombre de producto',
        'tipoProducto' => ['type' => 'Select2', 'data' => $tipoproductos, 'label' => 'Categoría'],
        'area' => ['type' => 'Select2', 'data' => $areas, 'label' => 'Área'],
        //'ordenCod'=>'Código de Orden', 
        'fechaDesde' => ['type' => 'DatePicker', 'label' => 'Desde'],
        'fechaHasta' => ['type' => 'DatePicker', 'label' => 'Hasta'],
    ];
    echo $this->render('_search', [
        'model' => $searchModel,
        'action' => 'productos-mas-vendidos',
        'fields' => $fields,
    ]); ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
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
                'attribute' => 'nombre_imagen',
                'label' => 'Imagen',
                'format' => 'raw',
                'value' => function ($data) {
                    if ($data['nombre_imagen']) {
                        $h = @fopen(Url::base(true) . '//uploads//' . $data['nombre_imagen'], 'r');
                        if ($h)
                            return Helper::ImgThumbailWidget(
                                Yii::$app->request->baseUrl . '//uploads//' . $data['nombre_imagen'],
                                $data['id'],
                                $data['codigo'] . ', ' . $data['nombre'],
                                'i' . $data['id'],
                                "70px",
                                "70px"
                            );
                    } else
                        return '';
                },
            ],
            [
                'attribute' => 'codigo',
                'label' => 'Código',
                'value' => function ($data) {
                    if ($data['codigo'])
                        return Html::a(
                            $data['codigo'],
                            ['/inventario/productos/view', 'id' => $data['id']],
                            ['target' => '_blank', 'data-pjax' => '0']
                        );
                    else
                        return '';
                },
            ],
            [
                'attribute' => 'nombre',
                'contentOptions' => ['style' => 'max-width:500px; white-space: pre-wrap;'],
            ],
            [
                'attribute' => 'desc',
                'contentOptions' => ['style' => 'max-width:500px; white-space: pre-wrap;'],
                'label' => 'Descripción',
            ],
            [
                'attribute' => 'tipoProducto',
                'label' => 'Categoría',
                'value' => function ($data) {
                    return $data['tipoProducto'];
                }
            ],
            /*[
                'attribute' => 'ordenCod',
                'label'=>'Orden',
                'value' => function($data) {
                    if($data['ordenCod'])
                        return Html::a($data['ordenCod'], ['/facturacion/ordenventas/view', 'id' => $data['ordenId']], 
                                            ['target' => '_blank', 'data-pjax' => '0']);
                    else
                        return '';
                },
             ],*/
            [
                'attribute' => 'cantVenta',
                'label' => 'Cantidad',
                'value' => function ($data) {
                    return $data['cantVenta'];
                }
            ],
        ],
    ]);  ?>

    <div id="ajax-loader" class="well row hidden">
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <h3>Procesando información<h3>
        </div>
        <div class="col-md-2"><?= Html::img('/InvFactServices/backend/web/images/loader.gif'); ?></div>
    </div>

    <?php Pjax::end();

    $this->registerJs('
        var inputName = null;
        jQuery("#p0").on("keyup", "input", function() {
                jQuery("#myPageSize-value").val(jQuery("#myPageSize").val()); //Para que se mantenga el valor del paginador
                jQuery(this).submit();  
                inputName = this.name;
            });',  yii\web\View::POS_READY);

    //Cuando PJAX recargue establecer el cursor en la busqueda     
    $this->registerJs('jQuery(document).on("pjax:success", "#p0", function(event) {
        if(inputName != null) {
            var el = $("input[name=\'"+inputName+"\']")[0];

            setInputCursor(el);
            inputName = null;
        }
    });');

    $this->registerJs("
        $(document).on('click', '.kv-file-zoom', function () {
            var id = $(this)[0].id;
            if(id != null) {
                $('#zoomHeader')[0].innerHTML = $('#i'+id)[0].title;
                $('#zoom_img')[0].src = $('#i'+id)[0].src;
                $('#modal_launch').click();
            }
        });",  yii\web\View::POS_READY);

    /* $this->registerJs('$("#W0").on("click", "thead", function() {
        console.log("dfsd");
    });');*/
    ?>
</div>