<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\ProductosOrdenServicio;

/**
 * ProductosOrdenServicioSearch represents the model behind the search form of `backend\modules\Facturacion\models\ProductosOrdenServicio`.
 */
class ProductosOrdenServicioSearch extends ProductosOrdenServicio
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'orden_id', 'producto_id', 'cant_productos', 'ejecutada'], 'integer'],
            [['cant_productos'], 'number'],
            [['fecha_ejecutada'], 'safe'],
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
        $query = ProductosOrdenServicio::find();

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
            'orden_id' => $this->orden_id,
            'producto_id' => $this->producto_id,
            'cant_productos' => $this->cant_productos,
            'ejecutada' => $this->ejecutada,
            'fecha_ejecutada' => $this->fecha_ejecutada,
        ]);

        return $dataProvider;
    }
}
