<?php

namespace backend\modules\Inventario\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\inventario\models\OrdenCompra;

/**
 * OrdenCompraSearch represents the model behind the search form of `app\modules\inventario\models\OrdenCompra`.
 */
class OrdenCompraSearch extends OrdenCompra
{
    public $myPageSize;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['fecha_creada', 'codigo', 'myPageSize'], 'safe'],
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
        $query = OrdenCompra::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
       /* $query->andFilterWhere([
            'id' => $this->id,
        ]);*/

        $query->andFilterWhere(['like', 'fecha_creada', $this->fecha_creada])
              ->andFilterWhere(['like', 'codigo', $this->codigo]);

        return $dataProvider;
    }
}
