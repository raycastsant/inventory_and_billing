<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\TipoCliente */

$this->title = 'Nuevo Tipo Cliente';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-cliente-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
