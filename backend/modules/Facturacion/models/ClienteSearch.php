<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\Cliente;

/**
 * ClienteSearch represents the model behind the search form of `backend\modules\Facturacion\models\Cliente`.
 */
class ClienteSearch extends Cliente
{
    public $myPageSize;
    public $tipoCliente;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tipo_cliente_id'], 'integer'],
            [['nombre', 'codigo', 'telefono', 'fax', 'direccion', 'email', 'tipoCliente', 'myPageSize'], 'safe'],
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
        $query = Cliente::find()->having(['eliminado'=> false]);
        $query->joinWith(['tipoCliente']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['tipoCliente'] = [
            'asc' => ['tipo_cliente.nombre' => SORT_ASC],
            'desc' => ['tipo_cliente.nombre' => SORT_DESC],
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
            //'tipo_cliente_id' => $this->tipo_cliente_id,
        ]);

        $query->andFilterWhere(['like', 'clientes.nombre', $this->nombre])
            ->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'tipo_cliente.nombre', $this->tipoCliente])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'fax', $this->fax])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
