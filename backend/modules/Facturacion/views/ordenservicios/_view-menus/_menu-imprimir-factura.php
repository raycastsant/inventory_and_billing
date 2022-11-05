<?php
    use yii\helpers\Html;

    $printOptions = ['class' => 'btn btn-default', 'target' => '_blank'];
    if( count($model->servicioTrabajadors) <= 0 )
        $printOptions['disabled'] = 'disabled';
    ?>
 
    <div class="btn-group pull-right">
        <?= Html::a('<span class="glyphicon glyphicon-print"></span> Factura', ['imprimir-factura', 'ordenid'=>$model->id, 'moneda2id'=>$model->moneda_id], $printOptions); ?>
        <?php if(!isset($printOptions['disabled'])) { ?>
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
        <?php } ?>
        <ul class="dropdown-menu">
            <?php 
                if($firma) { ?>
                    <li><?= Html::a('<span class="glyphicon glyphicon-edit"></span> Imprimir con firma', ['imprimir-factura', 'ordenid'=>$model->id, 'moneda2id'=>$model->moneda_id, 'printfirma'=>1], ['class' => 'btn', 'target' => '_blank']); ?></li>
                    <li class="divider"></li>
                <?php } 
                    if($model->moneda_id == 1) 
                    {
                ?>  
                        <li><?= Html::a('Imprimir en <b>CUP</b>', ['imprimir-factura', 'ordenid'=>$model->id, 'moneda2id'=>2], ['class' => 'btn', 'target' => '_blank']); ?></li>
                        <?php 
                        if($firma) { ?>
                            <li><?= Html::a('<span class="glyphicon glyphicon-edit"></span> Imprimir en <b>CUP</b> con firma', ['imprimir-factura', 'ordenid'=>$model->id, 'moneda2id'=>2, 'printfirma'=>1], ['class' => 'btn', 'target' => '_blank']); ?></li>
                        <?php } 
                    } 
                ?> 
        </ul>
    </div>