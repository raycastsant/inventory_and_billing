<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Economia\models\TipoGasto */

$this->title = 'Actualizar Tipo Gasto: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Gastos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="tipo-gasto-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
