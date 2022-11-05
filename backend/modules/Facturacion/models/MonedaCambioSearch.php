<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\MonedaCambio;

/**
 * MonedaCambioSearch represents the model behind the search form of `backend\modules\Facturacion\models\MonedaCambio`.
 */
class MonedaCambioSearch extends MonedaCambio
{
    public $m1;
    public $m2;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'm1_id', 'm2_id'], 'integer'],
            [['valor'], 'number'],
            [['m1', 'm2'], 'safe'],
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
    public function search($params) {
        $query = MonedaCambio::find();
        $query->joinWith(['m1']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);

        $dataProvider->sort->attributes['m1'] = [
            'asc' => ['monedas.nombre' => SORT_ASC],
            'desc' => ['monedas.nombre' => SORT_DESC],
            ];

        $dataProvider->sort->attributes['m2'] = [
            'asc' => ['monedas.nombre' => SORT_ASC],
            'desc' => ['monedas.nombre' => SORT_DESC],
            ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
       /*     'id' => $this->id,
            'm1_id' => $this->m1_id,
            'm2_id' => $this->m2_id,*/
            'valor' => $this->valor,
        ]);

        $query->andFilterWhere(['like', 'monedas.nombre', $this->m1])
              ->andFilterWhere(['like', 'monedas.nombre', $this->m2]);

        return $dataProvider;
    }
}
