<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\TipoCliente */

$this->title = 'Actualizar Tipo Cliente: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="tipo-cliente-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
