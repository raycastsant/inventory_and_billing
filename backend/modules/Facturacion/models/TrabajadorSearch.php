<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\Trabajador;

/**
 * TrabajadorSearch represents the model behind the search form of `backend\modules\Facturacion\models\Trabajador`.
 */
class TrabajadorSearch extends Trabajador
{
    public $myPageSize;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'eliminado'], 'integer'],
            [['nombre', 'ci', 'direccion', 'telefono', 'myPageSize'], 'safe'],
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
        $query = Trabajador::find()->having(['eliminado'=> false]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['nombre'=>SORT_ASC]],
        ]);

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
            //'eliminado' => $this->eliminado,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'ci', $this->ci])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'telefono', $this->telefono]);

        return $dataProvider;
    }
}
