<?php

namespace backend\modules\Reportes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Producto;
use backend\modules\Facturacion\models\ProductosOrdenVenta;


class GastosIngresosSearch extends Producto
{
    private $_query;

    public $myPageSize;
    public $tipoProducto;
    public $clienteNombre;
    public $fechaDesde;
    public $fechaHasta;
    public $cantVenta;
    public $area;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipoproducto_id'], 'integer'],
            [['nombre', 'codigo', 'desc', 'tipoProducto', 'fechaDesde', 'fechaHasta', 'area', 'gasto', 'ingreso', 'myPageSize'], 'safe'],
            ['fechaDesde', 'validateFechaDesde'],
        ];
    }

    public function validateFechaDesde($attribute, $params, $validator) {
        if( $this->fechaHasta!=null && strtotime($this->$attribute) > strtotime($this->fechaHasta) )
            $validator->addError($this, $attribute, 'La fecha DESDE debe ser menor');
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    private function executeQuery() {
        $query = new \yii\db\Query(); 

        $query->select(['*']);
        $query->from('gastos_ingresos_ventas');
        $query->groupBy(['tipoProducto', 'codigo', 'nombre']);
        //$query->orderBy(['tipoProducto', 'codigo', 'nombre']);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['=', 'tipoProducto', $this->tipoProducto])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'area_id', $this->area])
            ->andFilterWhere(['>=', 'fecha_cobrada', $this->fechaDesde])
            ->andFilterWhere(['<=', 'fecha_cobrada', $this->fechaHasta]);
            
        return $query;
    }

    public function search($params) {
        //!Importante, debe ir antes de la consulta!
        $this->load($params);
        
        /**Se buscan los productos sin tener en cuenta 
         * la existencia en almacen, siempre y cuando la orden correspondiente este FACTURADA, no 'Abierta' ni 'Cancelada'
         */
       /* $query->select(['productos.codigo', 'productos.nombre', 'productos.nombre_imagen', 'productos.id', 'productos.desc', 'tipoproductos.tipo as tipoProducto', 
                        'SUM(productos.costo) as gasto', 'SUM(productos_orden_venta.cantidad * productos_orden_venta.precio) as ingreso']);
        $query->from('productos');
        $query->innerJoin('tipoproductos', 'productos.tipoproducto_id=tipoproductos.id');
        $query->innerJoin('productos_orden_venta', 'productos.id=productos_orden_venta.producto_id');
        $query->innerJoin('orden_ventas', 'productos_orden_venta.orden_venta_id=orden_ventas.id');
        $query->andWhere('orden_ventas.estado_orden_id = 4 AND orden_ventas.eliminado=false');   //estado de la orden COBRADO
        $query->groupBy(['tipoproductos.tipo', 'productos.codigo', 'productos.nombre']);*/

        $this->_query = $this->executeQuery();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $this->_query,
        ]);

        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        $dataProvider->sort->attributes['tipoProducto'] = [
            'asc' => ['tipoProducto' => SORT_ASC],
            'desc' => ['tipoProducto' => SORT_DESC],
        ];
      
        $dataProvider->sort->attributes['nombre'] = [
            'asc' => ['nombre' => SORT_ASC],
            'desc' => ['ombre' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['desc'] = [
            'asc' => ['desc' => SORT_ASC],
            'desc' => ['desc' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['codigo'] = [
            'asc' => ['codigo' => SORT_ASC],
            'desc' => ['codigo' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['gasto'] = [
            'asc' => ['gasto' => SORT_ASC],
            'desc' => ['gasto' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['ingreso'] = [
            'asc' => ['ingreso' => SORT_ASC],
            'desc' => ['ingreso' => SORT_DESC],
        ];

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function getModelName() {
        return 'GastosIngresosSearch';
    }

   /*public static function getTotal($provider, $fieldName) {
        $total = 0;

        foreach($provider as $item)
            $total += $item[$fieldName];

        return $total;
    }*/

    public function getTotal($field) {
        return $this->_query->sum($field);
    }
}
