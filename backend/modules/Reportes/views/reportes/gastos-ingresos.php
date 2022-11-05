<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use miloschuman\highcharts\Highcharts;


$this->title = 'Reporte de Gastos e Ingresos';
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
        $dayfilter['weekly'] = 'Semanal';
        $dayfilter['monthly'] = 'Mensual';
        $dayfilter['yearly'] = 'Anual';

        $conceptos['servicios'] = 'Servicios';
        $conceptos['ventas'] = 'Ventas';

        $fields = ['fechaDesde'=>['type'=>'DatePicker', 'label'=>'Cobrado desde'], 
                   'concepto'=>['type'=>'Select2', 'data'=>$conceptos, 'label'=>'Concepto'],
                   'dayfilter'=>['type'=>'Select2', 'data'=>$dayfilter, 'label'=>'Mostrar', 'allowClear'=>false, 
                                 'selectedValue'=>'weekly', 'div_anchor'=>'2'],
                   'cliente'=>['type'=>'Select2', 'data'=>$clientes, 'label'=>'Cliente'],
                   'fechaHasta'=>['type'=>'DatePicker', 'label'=>'Hasta'], 
                   
                ];
        echo $this->render('_search', [
                            'model' => $searchModel, 
                            'action' => 'gastos-ingresos',
                            'fields' => $fields]); ?>

    <?php 
       /* $gastosSeries = [ 'name' => 'Gastos ('.round($totalGastos, 2).' cuc)', 
            'data' => $dataProvider['data']['gastos'], 
            'dataLabels' => ['color'=>'#ad0b0b'], 
            'color'=>'#ad0b0b' 
        ];*/
        $series = [[ 'name' => 'Beneficios ('.round($totalBeneficios, 2).' cuc)', 
                        'data' => $dataProvider['data']['beneficios'],
                        'dataLabels' => ['color'=>'#1602d2'], 
                        'color'=>'#1602d2' 
                    ],
                    [ 'name' => 'Ingresos ('.round($totalIngresos, 2).' cuc)', 
                        'data' => $dataProvider['data']['ingresos'], 
                        'dataLabels' => ['color'=>'#049c1b'], 
                        'color'=>'#049c1b' 
                    ]];

        if(isset($dataProvider['data']['gastos']))
            $series[] = [ 'name' => 'Gastos ('.round($totalGastos, 2).' cuc)', 
                            'data' => $dataProvider['data']['gastos'], 
                            'dataLabels' => ['color'=>'#ad0b0b'], 
                            'color'=>'#ad0b0b' 
                        ];

        echo Highcharts::widget([
            'options' => [
                'title' => false,
                'credits' => false,
                'chart' => [
                    'type' => 'spline',
                ],
                'xAxis' => [
                    'categories' => $dataProvider['categories']
                ],
                'yAxis' => [
                    'title' => false
                ],
                'plotOptions' => [
                        'spline' => ['dataLabels' => ['enabled' => 'true']],
                    ],
                'series' => $series,
            ]
         ]);
    ?>

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
