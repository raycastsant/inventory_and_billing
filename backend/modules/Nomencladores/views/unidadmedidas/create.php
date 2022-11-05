<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\UnidadMedida */

$this->title = 'Nueva';
$this->params['breadcrumbs'][] = ['label' => 'Listar', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unidad-medida-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
