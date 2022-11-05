<?php

namespace backend\modules\Facturacion\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DevolucionSearch represents the model behind the search form of `backend\modules\Facturacion\models\Devolucion`.
 */
class DevolucionVentaSearch extends Devolucion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'parcial'], 'integer'],
            [['fecha', 'ordenId'], 'safe'],
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
        $query = Devolucion::find();
        $query->innerJoin('orden_ventas', 'devoluciones.ordenId=orden_ventas.id');

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
           // 'id' => $this->id,
            'parcial' => $this->parcial,
            //'fecha' => $this->fecha,
        ]);

        $query->andFilterWhere(['like', 'orden_ventas.codigo', $this->ordenId])
        ->andFilterWhere(['like', 'fecha', $this->fecha]);

        return $dataProvider;
    }
}

/*extends DevolucionVenta
{
    public $fecha;
    public $parcial;
    public $orden;

    public function rules()
    {
        return [
           // [['id', 'parcial'], 'integer'],
            [['fecha', 'parcial', 'orden'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params) {
        $query = DevolucionVenta::find();
        //$query->andWhere(['is_venta' => true]);

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


        //$query->andFilterWhere(['like', 'causa', $this->causa]);

        return $dataProvider;
    }
}
*/