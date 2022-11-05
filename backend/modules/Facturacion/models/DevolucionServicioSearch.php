<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\Devolucion;

/**
 * DevolucionSearch represents the model behind the search form of `backend\modules\Facturacion\models\Devolucion`.
 */
class DevolucionServicioSearch extends Devolucion
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parcial'], 'integer'],
            [['fecha', 'ordenId'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Devolucion::find();
        $query->innerJoin('orden_servicios', 'devoluciones.ordenId=orden_servicios.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
           // 'id' => $this->id,
            'parcial' => $this->parcial,
            //'fecha' => $this->fecha,
        ]);

        $query->andFilterWhere(['like', 'orden_servicios.codigo', $this->ordenId])
        ->andFilterWhere(['like', 'fecha', $this->fecha]);

        return $dataProvider;
    }
}
