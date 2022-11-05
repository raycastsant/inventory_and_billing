<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Facturacion\models\Devolucion */

$this->title = 'Devolución parcial';

?>
<div class="devolucion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'modelForm' => $modelForm,
    ]) ?>

</div>
