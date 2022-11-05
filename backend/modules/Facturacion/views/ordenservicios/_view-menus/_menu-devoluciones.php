<?php
    use backend\modules\Facturacion\ESTADOS;
    use yii\helpers\Html;
?>

<div class="btn-group pull-right">
    <a class="btn dropdown-toggle btn-warning" data-toggle="dropdown" href="#">
        Devolución
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li> <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> Total', ['devoluciones/devolucion', 
                                'ordenId' => $model->id, 
                                'parcial' => false, 
                                'is_ventas' => false], 
                                [
                                    'data' => [
                                        'confirm' => '¿Está seguro que desea proceder con la devolución TOTAL?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
        </li>
        <li> <?=  Html::a('<span class="glyphicon glyphicon-minus"></span> Parcial', ['devoluciones/devolucion', 
                                'ordenId' => $model->id, 
                                'parcial' => true, 
                                'is_ventas' => false], 
                                [
                                    'data' => [
                                        'method' => 'post',
                                    ],
                                ]) ?>
        </li>
    </ul>
    </div><div class="pull-right">&nbsp;</div>
<div class="btn-group pull-right"></div>