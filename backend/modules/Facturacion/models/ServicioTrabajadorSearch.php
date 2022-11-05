<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\ServicioTrabajador;

/**
 * ServicioTrabajadorSearch represents the model behind the search form of `backend\modules\Facturacion\models\ServicioTrabajador`.
 */
class ServicioTrabajadorSearch extends ServicioTrabajador
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trabajador_id', 'orden_servicio_id', 'servicio_id'], 'integer'],
           // [['fecha'], 'safe'],
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
        $query = ServicioTrabajador::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'trabajador_id' => $this->trabajador_id,
            'orden_servicio_id' => $this->orden_servicio_id,
            'servicio_id' => $this->servicio_id,
          //  'fecha' => $this->fecha,
        ]);

        return $dataProvider;
    }
}
