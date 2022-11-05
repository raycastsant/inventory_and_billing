<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2;

    //Modal que muestra el Zoom de la imagen
    Modal::begin([
        'header' => '<h4 id="zoomHeader"></h4>',
        'toggleButton' => ['label'=>'', 'id'=>'modal_launch', 'style'=>'display:none'],
        'id' => 'modal_img',
        'closeButton'=>['id'=>'close_modal'],
        //'style' => ['max-height'=>'400px']
        ]); 
        echo '<img id="zoom_img" class="file-preview-image kv-preview-data file-zoom-detail" 
                src="" alt="" 
                style="width: auto; height: auto; max-width: 100%; max-height: 100%;">';
    Modal::end();

    $this->title = 'Existencias';
    $this->params['breadcrumbs'][] = $this->title;
    $or_value = "";
    if(isset($orvalue))
        $or_value = $orvalue;
?>

<div class="Productos-index">
    <?php Pjax::begin(['id' => 'p0']); ?>

    <legend>
        <div class="row">
            <div class="col-md-2">
                <h1><?= Html::a('Insertar nuevo', ['create'], ['class' => 'btn btn-success']) ?></h1>
            </div>
            <div class="col-md-10">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
    </legend>

    <?php 
    //Esta funcion es para los filtros, se declara aqui para que el componente de filtro la reconozca y poder reutilizarla
     $this->registerJs('
        function FilterValues(sender) {
            jQuery("#myPageSize-value").val(jQuery("#myPageSize").val()); //Para que se mantenga el valor del paginador
            jQuery(sender).submit();  
        };',  yii\web\View::POS_READY);
        
        $form = ActiveForm::begin([
            'action' => ['existencias'], 
            'method' => 'get',
            'id' => 'gen-search',
            'options' => ['data-pjax' => 1],
        ]); 
    ?>
    
    <div class="row">
        <div class="col-md-5">
            <input type="search" id="searchProd"  
                name="ProductoSearch[or_value]" placeholder="Buscar..." value="<?= $or_value; ?>"
                class="form-control form-white pull-right">
        </div>  
        <div class="col-md-4">  
            <?= $form->field($searchModel, 'tipoProducto')->widget(Select2::classname(), [
                    'data' =>  $tipoproductos,
                    'options' => [
                        'placeholder' => '-Seleccionar Categoría-',
                        'value' => $tipoProdValue,
                    ],
                    'language' => 'es',
                    'pluginOptions' => [
                        'allowClear' => true
                    ], 
                    'pluginEvents' => [
                        "select2:select" => "function() { FilterValues(this); }",
                        "select2:clearing" => "function() { FilterValues(this); }",
                    ],
                ])->label(false); 
            ?>
            <input type="hidden" id="myPageSize-value" name="ProductoSearch[myPageSize]" value="10"> 
        </div>  
        <div class="col-md-1" id="reset_div">
            <button id="resetBtn" class="btn btn-outline-secondary">Reiniciar</button>
        </div>
        <div class="col-md-2">
            <?= Html::a('<span class="glyphicon glyphicon-print"></span> Imprimir', 
                    ['imprimir-existencias', 'or_value'=>$or_value, 'tipoProdValue'=>$tipoProdValue],
                    ['class' => 'btn btn-default', 'target' => '_blank', 'data-pjax'=>0]
                ); ?>
        </div>
    </div> 

    <?php ActiveForm::end(); ?>  
    
        <div id="ajax-loader" class="well row hidden">
            <div class="col-md-3"></div>
            <div class="col-md-3"><h3>Procesando información<h3></div>
            <div class="col-md-2"><?= Html::img('/InvFactServices/backend/web/images/loader.gif'); ?></div>
        </div>

        <?php
             echo $this->render('_index-grid-columns', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel,]); 
             Pjax::end();  ?>
</div>
    <?php 
       //Eventos de los componentes de Filtros
       $this->registerJs('
            jQuery("#p0").on("keyup", "input", function() {
                //var $field = $(this);
                //if( $field.attr(\'id\') == "searchProd") {
                  
                    //jQuery("#myPageSize-value").val(jQuery("#myPageSize").val()); //Para que se mantenga el valor del paginador
                    //jQuery(this).submit();  

                    FilterValues(this);

                //}
                //else {
                  //  jQuery(this).change();
                //}
            });
            ',  yii\web\View::POS_READY);

        //Reset 
        $this->registerJs(' jQuery("#p0").on("click", "button", function() {
                if(!$(this).hasClass("kv-file-zoom")) {
                    var field = document.getElementById(\'searchProd\');
                    var filter_categoria = document.getElementById(\'productosearch-tipoproducto\');
                    var lastval = field.value;
                    
                    field.value = "";
                    filter_categoria.value = "";

                    if(lastval != "")
                        document.getElementById(\'gen-search\').submit();
                }
            });',  yii\web\View::POS_READY);

        //Loader Status GIF  
        $this->registerJs('jQuery("#p0").on("submit", function(){
                jQuery("#ajax-loader").removeClass("hidden");  
                jQuery("#w0").addClass("hidden"); 
            });',  yii\web\View::POS_READY);

        //Cuando PJAX recargue establecer el cursor en la busqueda     
        $this->registerJs('jQuery(document).on("pjax:success", "#p0", function(event){
            var el = document.getElementById(\'searchProd\');
            var caretPos = el.value.length;

            setInputCursor(el);
        });');
        
        $this->registerJs("
        $(document).on('click', '.kv-file-zoom', function () {
            var id = $(this)[0].id;
            if(id != null) {
                $('#zoomHeader')[0].innerHTML = $('#i'+id)[0].title;
                $('#zoom_img')[0].src = $('#i'+id)[0].src;
                $('#modal_launch').click();
            }
        });
            ",  yii\web\View::POS_READY);
        ?>

           

      
    
