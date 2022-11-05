<?php

namespace backend\modules\Facturacion\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\OrdenVenta;

/**
 * OrdenVentaSearch represents the model behind the search form of `backend\modules\Facturacion\models\OrdenVenta`.
 */
class OrdenVentaSearch extends OrdenVenta
{
    public $myPageSize;
    public $cliente;
    public $estadoOrden; 
    public $area;
    public $vehiculo;
    public $moneda;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cliente_id', 'estado_orden_id', 'area_id', 'moneda'], 'integer'],
            [['codigo', 'fecha_iniciada', 'fecha_facturada', 'fecha_cobrada', 'cliente', 'estadoOrden', 
              'area', 'vehiculo', 'myPageSize', 'moneda'], 'safe'],
            [['precio_estimado'], 'number'],
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
        $session = Yii::$app->session;
        $area_id = -1;
        if($session->has('area'))
            $area_id = $session->get('area');

        if($area_id > 0)
            $query = OrdenVenta::find()->having(['eliminado'=> false, 'area_id'=> $area_id]);
        else
            $query = OrdenVenta::find()->having(['eliminado'=> false]);

        $query->joinWith(['cliente', 'estadoOrden', 'vehiculo', 'area']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->defaultOrder['codigo'] = SORT_DESC;

        $dataProvider->sort->attributes['cliente'] = [
            'asc' => ['clientes.nombre' => SORT_ASC],
            'desc' => ['clientes.nombre' => SORT_DESC],
            ];
        $dataProvider->sort->attributes['estadoOrden'] = [
            'asc' => ['estado_orden.estado' => SORT_ASC],
            'desc' => ['estado_orden.estado' => SORT_DESC],
            ];
        $dataProvider->sort->attributes['vehiculo'] = [
            'asc' => ['vehiculos.chapa' => SORT_ASC],
            'desc' => ['vehiculos.chapa' => SORT_DESC],
            ];
        $dataProvider->sort->attributes['area'] = [
            'asc' => ['areas.nombre' => SORT_ASC],
            'desc' => ['areas.nombre' => SORT_DESC],
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
            'moneda_id' => $this->moneda,
            'precio_estimado' => $this->precio_estimado,
        ]);

        $query->andFilterWhere(['like', 'orden_ventas.codigo', $this->codigo])
        ->andFilterWhere(['like', 'clientes.nombre', $this->cliente])
        ->andFilterWhere(['like', 'fecha_iniciada', $this->fecha_iniciada])
        ->andFilterWhere(['like', 'estado_orden.estado', $this->estadoOrden])
        ->andFilterWhere(['like', 'areas.nombre', $this->area])
        ->andFilterWhere(['like', 'vehiculos.chapa', $this->vehiculo]);

        return $dataProvider;
    }
}
