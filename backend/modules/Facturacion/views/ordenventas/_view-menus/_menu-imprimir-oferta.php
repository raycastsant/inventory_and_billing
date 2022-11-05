<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Reportes\models\ActaEntrega;
use backend\modules\Reportes\models\ActaServicio;

    $printOfertsOptions = ['class' => 'btn btn-default', 'target' => '_blank'];
    if(count($model->productosOrdenVentas) <= 0)
        $printOfertsOptions['disabled'] = 'disabled';

    $model_acta = new ActaEntrega();
    $acta_servicio = new ActaServicio();
?>

    <!-- Form Acta Servicio -->
    <div class="btn-group pull-right">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['target' => '_blank'],
                                         'action'=>Yii::$app->urlManager->createUrl(['reportes/reportes/acta-servicio-form'])]); 
                    echo Html::submitButton('<span class="glyphicon glyphicon-print"></span> Orden-servicio', 
                    ['class' => 'btn btn-default']); 
            
            echo $form->field($acta_servicio, 'cliente')->hiddenInput(['value' => $model->cliente->nombre])->label(false);  
            if(isset($model->vehiculo)) { 
                echo $form->field($acta_servicio, 'marca')->hiddenInput(['value' => $model->vehiculo->marca])->label(false);  
                echo $form->field($acta_servicio, 'modelo')->hiddenInput(['value' => $model->vehiculo->modelo])->label(false);  
                echo $form->field($acta_servicio, 'matricula')->hiddenInput(['value' => $model->vehiculo->chapa])->label(false);  
            }
        ?>
        <?php ActiveForm::end(); ?>
    </div> 
    <!-- Form Acta Entrega -->
    <div class="btn-group pull-right">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['target' => '_blank'],
                                         'action'=>Yii::$app->urlManager->createUrl(['reportes/reportes/acta-entrega-form'])]); 
                    echo Html::submitButton('<span class="glyphicon glyphicon-print"></span> Acta-entrega', 
                    ['class' => 'btn btn-default']); 
            
            echo $form->field($model_acta, 'cliente')->hiddenInput(['value' => $model->cliente->nombre])->label(false); 
            if(isset($model->vehiculo)) { 
                echo $form->field($model_acta, 'marca')->hiddenInput(['value' => $model->vehiculo->marca])->label(false);  
                echo $form->field($model_acta, 'modelo')->hiddenInput(['value' => $model->vehiculo->modelo])->label(false);  
                echo $form->field($model_acta, 'matricula')->hiddenInput(['value' => $model->vehiculo->chapa])->label(false); 
            } 
        ?>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="btn-group pull-right">
        <?= Html::a('<span class="glyphicon glyphicon-print"></span> Oferta', ['imprimir-oferta', 'ordenid'=>$model->id, 'moneda2id'=>$model->moneda_id], $printOfertsOptions); ?>
        <?php if(!isset($printOfertsOptions['disabled'])) { ?>
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
        <?php } ?>
        <ul class="dropdown-menu">
            <?php 
                if($firma) { ?>
                    <li><?= Html::a('<span class="glyphicon glyphicon-edit"></span> Imprimir con firma', ['imprimir-oferta', 'ordenid'=>$model->id, 'moneda2id'=>$model->moneda_id, 'printfirma'=>1], ['class' => 'btn', 'target' => '_blank']); ?></li>
                    <li class="divider"></li>
                <?php } 
                    if($model->moneda_id == 1) 
                    {
                ?>
                        <li><?= Html::a('Imprimir en <b>CUP</b>', ['imprimir-oferta', 'ordenid'=>$model->id, 'moneda2id'=>2], ['class' => 'btn', 'target' => '_blank']); ?></li>
                        <?php 
                        if($firma) { ?>
                            <li><?= Html::a('<span class="glyphicon glyphicon-edit"></span> Imprimir en <b>CUP</b> con firma', ['imprimir-oferta', 'ordenid'=>$model->id, 'moneda2id'=>2, 'printfirma'=>1], ['class' => 'btn', 'target' => '_blank']); ?></li>
                        <?php } 
                    }
                ?>
        </ul>
    </div>