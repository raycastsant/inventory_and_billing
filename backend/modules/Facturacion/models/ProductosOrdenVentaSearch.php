<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\ProductosOrdenVenta;

/**
 * ProductosOrdenVentaSearch represents the model behind the search form of `backend\modules\Facturacion\models\ProductosOrdenVenta`.
 */
class ProductosOrdenVentaSearch extends ProductosOrdenVenta
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'producto_id', 'orden_venta_id', 'cantidad'], 'integer'],
            [['cantidad'], 'number'],
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
        $query = ProductosOrdenVenta::find();

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
            'producto_id' => $this->producto_id,
            'orden_venta_id' => $this->orden_venta_id,
            'cantidad' => $this->cantidad,
        ]);

        return $dataProvider;
    }
}
