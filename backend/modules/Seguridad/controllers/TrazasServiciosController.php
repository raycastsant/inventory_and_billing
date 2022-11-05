<?php

namespace backend\modules\Seguridad\controllers;

use backend\modules\Seguridad\models\TrazasServicio;
use yii\data\ActiveDataProvider;
use Yii;

class TrazasServiciosController extends \yii\web\Controller
{
    public $user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user'], 'safe'],
        ];
    }

    public function actionIndex()  {
        return $this->render('index');
    }

    public static function Insert($desc, $orden_servicio_id) {
        $model = new TrazasServicio();
        $model->descripcion = $desc;
        $model->orden_servicio_id = $orden_servicio_id;
        $model->user_id = Yii::$app->getUser()->id;
        $model->fecha = date("Y-m-d H:i:s");

        return ($model->validate() && $model->save());
    }

    /**Obtiene un ActiveDataProvider de las trazas 
     * de una orden de servicios */
    public static function getOrdenTrazas($ordenId) {
        $query = TrazasServicio::find()->having(['orden_servicio_id'=>$ordenId])->orderBy(['fecha' => SORT_DESC]);
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
