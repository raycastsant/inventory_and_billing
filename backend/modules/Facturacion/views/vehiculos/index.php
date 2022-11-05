<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\Facturacion\models\VehiculoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vehículos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehiculo-index">
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
        //'afterAjaxUpdate' => 'afterAjaxUpdate',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'cliente',
                'label'=>'Cliente',
                'value'=>'cliente.nombre',
                'contentOptions' => ['style' => 'max-width:400px; white-space: pre-wrap;'],
             ],
            'chapa',
            'modelo',
            'marca',
            'fabricante',
            'anno',
            'codigo_motor',
            'codigo_alternador',
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
                            'data-confirm' => '¿Está seguro que desea eliminar el vehículo?',
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

    <!-- <div id="ajax-loader" class="well row hidden">
        <div class="col-md-3"></div>
        <div class="col-md-3"><h3>Procesando información<h3></div>
        <div class="col-md-2"><?= Html::img('/InvFactServices/backend/web/images/loader.gif'); ?></div>
    </div> -->

    <?php Pjax::end(); ?>
    
<?php 
echo Yii::$app->view->renderFile(Yii::getAlias('@app').'/views/_FilterFocusScript.php');

/* $this->registerJs('$(document).on("pjax:send", function() {
    jQuery("#ajax-loader").removeClass("hidden");  
    jQuery("#w0").addClass("hidden");
});',  yii\web\View::POS_READY); */

/*$this->registerJs('$(document).on("pjax:send", function() {
    jQuery("#ajax-loader").removeClass("hidden");
    jQuery("#w0")[0].children[1].children[1].innerHtml = jQuery("#ajax-loader");
});',  yii\web\View::POS_READY);*/

/*ob_start(); // output buffer the javascript to register later ?>
    <script>
       $(function() {
            setupGridView();
        });

        // Setup the filter(s) controls
        function setupGridView(grid) {
            if(grid==null)
                grid = '.grid-view tr.filters';
            // Default handler for filter change event
            $('input,select', grid).change(function() {
                var grid = $(this).closest('.grid-view');
                $(document).data(grid.attr('id')+'-lastFocused', this.name);
            });
        }

        // Default handler for beforeAjaxUpdate event
        function afterAjaxUpdate(id) {
            var grid = $('#'+id);
            var lf = $(document).data(grid.attr('id')+'-lastFocused');
            // If the function was not activated
            if(lf == null) return;
            // Get the control
            fe = $('[name="'+lf+'"]', grid);
            // If the control exists..
            if(fe!=null) {
                if(fe.get(0).tagName == 'INPUT' && fe.attr('type') == 'text')
                    // Focus and place the cursor at the end
                    fe.cursorEnd();
                else
                    // Just focus
                    fe.focus();
            }
            // Setup the new filter controls
            setupGridView(grid);
        };

        // Place the cursor at the end of the text field
        jQuery.fn.cursorEnd = function() {
            return this.each(function() {
                if(this.setSelectionRange) {
                    this.focus();
                    this.setSelectionRange(this.value.length,this.value.length);
                }
                else 
                if (this.createTextRange) {
                    var range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', this.value.length);
                    range.moveStart('character', this.value.length);
                    range.select();
                }
                return false;
            });
        }

        jQuery("#p0").on("keyup", "input", function() {
            jQuery(this).change();
        });

            //Cuando PJAX recargue establecer el cursor en la busqueda     
        jQuery(document).on("pjax:success", "#p0", function(event) {
            afterAjaxUpdate("w0");
        });
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); */?>

</div>
