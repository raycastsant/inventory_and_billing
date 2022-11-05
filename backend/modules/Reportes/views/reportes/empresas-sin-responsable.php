<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Empresas sin responsable';
?>
<div class="empresasinresp-index">
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
        $fields = ['nombre'=>'Nombre', 'telefono'=>'Teléfono', 'email'=>'Email', 'direccion'=>'Dirección',];
        echo $this->render('_search', [
                            'model' => $searchModel, 
                            'action' => 'empresas-sin-responsable',
                            'fields' => $fields, ]); ?>

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
                'attribute' => 'nombre',
                'label'=>'Nombre',
                'value' => function($data) {
                    if($data['nombre'])
                        return Html::a($data['nombre'], ['/facturacion/clientes/view', 'id' => $data['cid']], 
                                            ['target' => '_blank', 'data-pjax' => '0']);
                    else
                        return '';
                },
            ],
            'telefono',
            'email',
             [
                'attribute' => 'direccion',
                'label'=>'Dirección',
             ],
        ],
    ]);  ?>

    <div id="ajax-loader" class="well row hidden">
        <div class="col-md-3"></div>
        <div class="col-md-3"><h3>Procesando información<h3></div>
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
