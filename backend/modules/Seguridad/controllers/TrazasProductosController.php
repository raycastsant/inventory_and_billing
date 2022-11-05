<?php

namespace backend\modules\Seguridad\controllers;

use backend\modules\Seguridad\models\TrazasProducto;
use yii\data\ActiveDataProvider;
use Yii;

class TrazasProductosController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public static function Insert($desc, $product_id) {
        $model = new TrazasProducto();
        $model->descripcion = $desc;
        $model->producto_id = $product_id;
        $model->user_id = Yii::$app->getUser()->id;
        $model->fecha = date("Y-m-d H:i:s");

        return ($model->validate() && $model->save());
    }

     /**Obtiene un ActiveDataProvider de las trazas 
     * de un producto */
    public static function getProductoTrazas($id) {
        $query = TrazasProducto::find()->having(['producto_id'=>$id])->orderBy(['fecha' => SORT_DESC]);
       /* $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);*/

        $query->joinWith(['user']);

        return $query->asArray()->all();

        /*$dataProvider->sort->attributes['user'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
            ];

        return $dataProvider;*/
    }
}
