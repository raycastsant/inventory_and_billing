<?php

namespace backend\modules\Nomencladores\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Nomencladores\models\UnidadMedida;

/**
 * UnidadmedidaSearch represents the model behind the search form of `backend\modules\Nomencladores\models\UnidadMedida`.
 */
class UnidadmedidaSearch extends UnidadMedida
{
    public $myPageSize;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['unidad_medida', 'myPageSize'], 'safe'],
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
        $query = UnidadMedida::find()->where(['eliminado'=> false]);

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
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'unidad_medida', $this->unidad_medida]);

        return $dataProvider;
    }
}
