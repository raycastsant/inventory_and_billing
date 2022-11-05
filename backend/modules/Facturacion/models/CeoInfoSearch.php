<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\CeoInfo;

/**
 * CeoInfoSearch represents the model behind the search form of `backend\modules\Facturacion\models\CeoInfo`.
 */
class CeoInfoSearch extends CeoInfo
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nombre', 'ci', 'cuenta_cuc', 'cuenta_mn', 'sucursal', 'direccion', 'telefono', 'email', 'actividad', 'regime'], 'safe'],
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
        $query = CeoInfo::find();

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
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'ci', $this->ci])
            ->andFilterWhere(['like', 'cuenta_cuc', $this->cuenta_cuc])
            ->andFilterWhere(['like', 'cuenta_mn', $this->cuenta_mn])
            ->andFilterWhere(['like', 'sucursal', $this->sucursal])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'actividad', $this->actividad])
            ->andFilterWhere(['like', 'regime', $this->regime]);

        return $dataProvider;
    }
}
