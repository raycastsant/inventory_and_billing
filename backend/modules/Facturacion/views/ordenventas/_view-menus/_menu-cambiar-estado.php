<?php
    use backend\modules\Facturacion\ESTADOS;
    use yii\helpers\Html;

    if($estado != ESTADOS::COBRADO) { ?>
        <div class="btn-group pull-right">
        <a class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">
            Cambiar estado
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
        <?php 
            if($estado == ESTADOS::ABIERTO) { 
                if(count($model->productosOrdenVentas) > 0) {
        ?>
            <li> <?= Html::a('<span class="glyphicon glyphicon-share"></span> Facturar', ['facturar-orden', 'ordenId' => $model->id, 'cobrado' => false], [
                    'disabled',
                    'data' => [
                        'confirm' => '¿Está seguro que desea FACTURAR?',
                        'method' => 'post',
                    ],
                ]) ?></li>
            <li> <?=  Html::a('<span class="glyphicon glyphicon-check"></span> Facturar y cobrar', ['facturar-orden', 'ordenId' => $model->id, 'cobrado' => true], [
                            //'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => '¿Está seguro que desea FACTURAR y establecer el estado COBRADO?',
                                'method' => 'post',
                            ],
                        ]) ?></li>
        <?php 
                }
            }
            else
            if($estado == ESTADOS::FACTURADO) { ?>
                <li> <?=  Html::a('<span class="glyphicon glyphicon-check"></span> Cobrado', ['set-cobrada', 'ordenId' => $model->id, 'cobrado' => true], [
                            // 'class' => 'btn btn-success',
                            'data' => [
                                'confirm' => '¿Está seguro que desea cambiar el estado a COBRADO?',
                                'method' => 'post',
                            ],
                        ]) ?></li>
    <?php   }  ?>
            <li> <?= Html::a('<span class="glyphicon glyphicon-remove"></span> Cancelar orden', ['cancelar-orden', 'ordenId' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => '¿Está seguro que desea CANCELAR la orden?',
                                'method' => 'post',
                            ], ])  ?></li>
        </ul>
        </div><div class="pull-right">&nbsp;</div>
        <div class="btn-group pull-right"></div>
    <?php   
    }
    else { 
        echo Html::a('<span class="glyphicon glyphicon-remove"></span> Cancelar orden', ['cancelar-orden', 'ordenId' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => '¿Está seguro que desea CANCELAR la orden?',
                'method' => 'post',
            ], ]) ;
    }
?>