<?php

namespace backend\modules\Seguridad\controllers;

use backend\modules\Seguridad\models\TrazasVenta;
use yii\data\ActiveDataProvider;
use Yii;

class TrazasVentasController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public static function Insert($desc, $orden_venta_id) {
        $model = new TrazasVenta();
        $model->descripcion = $desc;
        $model->orden_venta_id = $orden_venta_id;
        $model->user_id = Yii::$app->getUser()->id;
        $model->fecha = date("Y-m-d H:i:s");

        return ($model->validate() && $model->save());
    }

     /**Obtiene un ActiveDataProvider de las trazas 
     * de una orden de ventas */
    public static function getOrdenTrazas($ordenId) {
        $query = TrazasVenta::find()->having(['orden_venta_id'=>$ordenId])->orderBy(['fecha' => SORT_DESC]);
        /*$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);*/

        $query->joinWith(['user']);

        return $query->asArray()->all();
        
       /* $dataProvider->sort->attributes['user'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
            ];

        return $dataProvider;*/
    }
}
