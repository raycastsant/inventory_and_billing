<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\Vehiculo;

/**
 * VehiculoSearch represents the model behind the search form of `backend\modules\Facturacion\models\Vehiculo`.
 */
class VehiculoSearch extends Vehiculo
{
    public $myPageSize;
    public $cliente;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cliente_id'], 'integer'],
            [['chapa', 'modelo', 'fabricante', 'codigo_alternador', 'codigo_motor', 'anno', 'cliente', 'marca', 'myPageSize'], 'safe'],
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
        $query = Vehiculo::find();
        $query->andFilterWhere(['clientes.eliminado'=> false]);
        $query->andFilterWhere(['vehiculos.eliminado'=> false]);
        $query->joinWith(['cliente']); //->orderBy('chapa');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['chapa'=>SORT_ASC]],
        ]);

        $dataProvider->sort->attributes['cliente'] = [
            'asc' => ['clientes.nombre' => SORT_ASC],
            'desc' => ['clientes.nombre' => SORT_DESC],
        ];

        $this->load($params);
        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'anno' => $this->anno,
        ]);

        $query->andFilterWhere(['like', 'chapa', $this->chapa])
            ->andFilterWhere(['like', 'modelo', $this->modelo])
            ->andFilterWhere(['like', 'clientes.nombre', $this->cliente])
            ->andFilterWhere(['like', 'fabricante', $this->fabricante])
            ->andFilterWhere(['like', 'codigo_motor', $this->codigo_motor])
            ->andFilterWhere(['like', 'codigo_alternador', $this->codigo_alternador])
            ->andFilterWhere(['like', 'marca', $this->marca]);

        return $dataProvider;
    }
}
