<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\Nomencladores\models\Area */

$this->title = 'Nueva';
$this->params['breadcrumbs'][] = ['label' => 'Áreas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="area-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
