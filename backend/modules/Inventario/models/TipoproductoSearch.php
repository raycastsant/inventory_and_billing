<?php

namespace backend\modules\Inventario\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Tipoproducto;

/**
 * TipoproductoSearch represents the model behind the search form of `backend\modules\Inventario\models\Tipoproducto`.
 */
class TipoproductoSearch extends Tipoproducto
{
    public $myPageSize;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
         //   [['id'], 'integer'],
            [['tipo', 'myPageSize'], 'safe'],
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
        $query = Tipoproducto::find()->where(['eliminado' => false]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['tipo'=>SORT_ASC]],
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
        ]);

        $query->andFilterWhere(['like', 'tipo', $this->tipo]);

        return $dataProvider;
    }
}
