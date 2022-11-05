<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\inventario\models\OrdenCompra */

$this->title = "Orden de compra";
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="orden-compra-view">
    <p> 
        <h1><?= Html::encode($this->title) ?>
            <?= Html::a('Crear nueva', ['create'], ['class' => 'btn btn-success']) ?>
        </h1>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'fecha_creada',
            'codigo'
        ],
    ]); 

    /**Lista de productos */ 
    $montoProd = 0;
    if(count($model->ordenCompraProductos) > 0) { 
        $moneda = $model->moneda->nombre;
    ?>
        <table class="table table-striped table-bordered">
            <tr><th>Producto</th><th>Código</th><th>Cantidad</th><th>Costo</th><th>Monto</th></tr>
    <?php   
            $costo;
            foreach($model->ordenCompraProductos as $prodOrden) { ?>
            <tr>
                <td><?= $prodOrden->producto->nombre ?> </td>
                <td><?= $prodOrden->producto->codigo ?> </td>
                <td><?= $prodOrden->cantidad ?></td>
                <td><?= $prodOrden->costo.' '.$moneda ?></td>
                <td><?= ($prodOrden->costo*$prodOrden->cantidad).' '.$moneda ?></td>
            </tr>
    <?php
            } ?>
        </table>
  <?php } ?>  

</div>
