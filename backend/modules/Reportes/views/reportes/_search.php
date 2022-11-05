<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\ServicioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div>
    <?php $form = ActiveForm::begin([
        'action' => $action,
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'id' => 'searchForm',
        ],
    ]); ?>
    <div class="row">
    <?php      
        foreach($fields as $field=>$options) {
            $div_anchor = '3';
            if( isset($options['div_anchor']) )
                $div_anchor = $options['div_anchor'];
            
            echo '<div class="col-md-'.$div_anchor.'">';

            if(isset($options['type'])) {
                if($options['type'] == 'Select2') {   //SELECT 2
                    
                    $allowClear = true;
                    if( isset($options['allowClear']) )
                        $allowClear = $options['allowClear'];

                    $selectedValue = '';
                    $pholder = '-Seleccionar-';
                    if( isset($options['selectedValue']) ) {
                        $selectedValue = $options['selectedValue'];
                        $pholder = false;
                    }

                    echo $form->field($model, $field)->widget(Select2::classname(), [
                        'data' =>  $options['data'],
                        'options' => ['placeholder' => $pholder],
                        'language' => 'es',
                        'value' => $selectedValue,
                        'pluginOptions' => [
                            'allowClear' => $allowClear,
                        ], ])->label($options['label']);
                }
                else
                if($options['type'] == 'DatePicker') {  //DATE PICKER
                    echo $form->field($model, $field)->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Seleccionar fecha...'],
                        'language' => 'es',
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'autoclose' => true,
                        ]
                    ])->label($options['label']);
                }
            }
            else
                echo $form->field($model, $field)->label($options);
            echo "</div>";
        }
    ?>
    <input type="hidden" id="myPageSize-value" name="<?= $model->getModelName()?>[myPageSize]" value="10"> 
    </div>

    <div class="form-group">
        <?php //echo Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?php //echo Html::resetButton('Reiniciar', ['class' => 'btn btn-outline-secondary', 'id'=>'resetBtn']) ?>
    </div>

    <?php ActiveForm::end(); 

   // $this->registerJs('var $el;',  yii\web\View::POS_READY);

    $this->registerJs('
        jQuery(".form-control").on("change", function() {
                jQuery("#myPageSize-value").val(jQuery("#myPageSize").val()); //Para que se mantenga el valor del paginador
                jQuery(this).submit();  
            });',  yii\web\View::POS_READY);

  /*  $this->registerJs('jQuery("#p0").on("submit", function(){
            jQuery("#ajax-loader").removeClass("hidden");  
            jQuery("#w0").addClass("hidden");
        });',  yii\web\View::POS_READY);*/

    $this->registerJs('$(document).on("pjax:send", function() {
        jQuery("#ajax-loader").removeClass("hidden");  
        jQuery("#w0").addClass("hidden");
    });',  yii\web\View::POS_READY);

    //Reset button de los filtros de los Reportes
    $this->registerJs('jQuery("#resetBtn").on("click", function(){
        var input = null;
        var hasChanges = false;  //Para controlar que no se haga un SUBMIT si no hay datos que filtrar

        jQuery("#searchForm .form-control").each(function() {
            if( $(this)[0].id != "productosmasvendidossearch-cantventa") {

                if(!hasChanges && $(this).val().length > 0)
                    hasChanges = true;

                $(this).val("");
            }
            else
                input = $(this);
        });

       /* //Para el reporte de ventas por clientes 
        if(input == null)
            input = jQuery("#ventasporclientessearch-clientenombre");

        if(hasChanges && input != null)
            input.change();*/
        
        if(hasChanges)
            jQuery("#searchForm").submit();

    });',  yii\web\View::POS_READY);
    ?>
</div>
