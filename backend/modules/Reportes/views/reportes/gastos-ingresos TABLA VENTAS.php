<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;


$this->title = 'Reporte de Gastos e Ingresos de las ventas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
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
        'codigo' => 'Código de producto', 'nombre' => 'Nombre de producto',
        'tipoProducto' => ['type' => 'Select2', 'data' => $tipoproductos, 'label' => 'Categoría de producto'],
        'area' => ['type' => 'Select2', 'data' => $areas, 'label' => 'Área'],
        'fechaDesde' => ['type' => 'DatePicker', 'label' => 'Cobrado desde'],
        'fechaHasta' => ['type' => 'DatePicker', 'label' => 'Hasta'],
    ];
    echo $this->render('_search', [
        'model' => $searchModel,
        'action' => 'gastos-ingresos',
        'fields' => $fields
    ]); ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
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
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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
                    if ($data['codigo'])
                        return $data['codigo'];
                    else
                        return '';
                },
            ],
            [
                'attribute' => 'nombre',
                'contentOptions' => ['style' => 'max-width:500px; white-space: pre-wrap;'],
            ],
            [
                'attribute' => 'nombre_imagen',
                'label' => 'Imagen',
                'format' => 'html',
                'value' => function ($data) {
                    if ($data['nombre_imagen'])
                        return Html::img(
                            Yii::$app->request->baseUrl . '//uploads//' . $data['nombre_imagen'],
                            ['width' => '70px', 'height' => '70px']
                        );
                    else
                        return '';
                },
            ],
            [
                'attribute' => 'desc',
                'label' => 'Descripción',
                'contentOptions' => ['style' => 'max-width:500px; white-space: pre-wrap;'],
            ],
            [
                'attribute' => 'tipoProducto',
                'label' => 'Categoría',
                'value' => function ($data) {
                    return $data['tipoProducto'];
                },
                'format' => 'raw',
                'footer' => '<b>Totales</b>',
            ],
            [
                'attribute' => 'gasto',
                'label' => 'Gastos',
                'value' => function ($data) {
                    return round($data['gasto'], 2) . ' cuc';
                },
                'format' => 'raw',
                'footer' => '<h4><span class="label warning-nav-badge">' . round($totalGastos, 2) . ' CUC</span></h4>',
            ],
            [
                'attribute' => 'ingreso',
                'label' => 'Ingresos',
                'value' => function ($data) {
                    return round($data['ingreso'], 2) . ' cuc';
                },
                'format' => 'raw',
                'footer' => '<h4><span class="label green-nav-badge">' . round($totalIngresos, 2) . ' CUC</span></h4>',
            ],
        ],
    ]); ?>

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
    ?>
</div>