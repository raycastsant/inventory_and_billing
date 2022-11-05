<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\UnidadMedida */

$this->title = 'Actualizar';
$this->params['breadcrumbs'][] = ['label' => 'Listar', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="unidad-medida-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
