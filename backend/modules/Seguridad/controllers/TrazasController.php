<?php

namespace backend\modules\Seguridad\controllers;

use backend\modules\Seguridad\models\Traza;
use Yii;

class TrazasController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public static function Insert($desc, $tableName) {
        $model = new Traza();
        $model->descripcion = $desc;
        $model->nombre_tabla = $tableName;
        $model->user_id = Yii::$app->getUser()->id;
        $model->fecha = date("Y-m-d H:i:s");

        return ($model->validate() && $model->save());
    }

}
