<?php

namespace backend\modules\Reportes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Producto;
use backend\modules\Facturacion\models\ProductosOrdenVenta;


/**
 * VehiculoSearch represents the model behind the search form of `backend\modules\Facturacion\models\Vehiculo`.
 */
class VentasPorClientesSearch extends Producto
{
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
            [['tipoproducto_id', 'cantVenta'], 'integer'],
            [['nombre', 'codigo', 'description', 'desc_ampliada', 'clienteNombre', 'tipoProducto', 'fechaDesde', 'fechaHasta', 'cantVenta', 'area', 'myPageSize'], 'safe'],
            ['fechaDesde', 'validateFechaDesde'],
            [['costo', 'precio'], 'number'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        //!Importante, debe ir antes de la consulta!
        $this->load($params);
        
        /**Se buscan los productos sin tener en cuenta 
         * la existencia en almacen, siempre y cuando la orden correspondiente este FACTURADA, no 'Abierta' ni 'Cancelada'
         */
        $query = new \yii\db\Query();    //Producto::find();

       /* $query->select(['productos.codigo', 'productos.nombre', 'productos.nombre_imagen', 'productos.id', 'productos.desc', 'tipoproductos.tipo as tipoProducto', 
                        'orden_ventas.codigo as ordenCod', 'productos_orden_venta.cantidad as cantVenta', 'clientes.nombre as clienteNombre', 'clientes.id as clienteId']);
        $query->from('productos');
        $query->innerJoin('tipoproductos', 'productos.tipoproducto_id=tipoproductos.id');
        $query->innerJoin('productos_orden_venta', 'productos.id=productos_orden_venta.producto_id');
        $query->innerJoin('orden_ventas', 'productos_orden_venta.orden_venta_id=orden_ventas.id');
        $query->innerJoin('clientes', 'orden_ventas.cliente_id=clientes.id');
       // $query->innerJoin('areas', 'orden_ventas.area_id=areas.id');
        $query->andWhere('orden_ventas.estado_orden_id >= 3 AND orden_ventas.eliminado=false');
        $query->groupBy(['clientes.nombre', 'productos.codigo', 'productos.nombre', 'productos_orden_venta.cantidad',   
                        'tipoproductos.tipo']);*/
        
        $query->select(['codigo', 'nombre', 'nombre_imagen', 'id', 'description', 'tipoProducto', 'tipoProductoId', 'ordenCod', 'sum(cantVenta) as cantVenta', 'clienteNombre', 'clienteId']);
        $query->from('view_ventas_clientes');
        $query->groupBy(['clienteNombre', 'tipoProducto', 'codigo', 'nombre']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        $dataProvider->sort->attributes['tipoProducto'] = [
            'asc' => ['tipoProducto' => SORT_ASC],
            'desc' => ['tipoProducto' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cantVenta'] = [
            'asc' => ['cantVenta' => SORT_ASC],
            'desc' => ['cantVenta' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['nombre'] = [
            'asc' => ['nombre' => SORT_ASC],
            'desc' => ['nombre' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['description'] = [
            'asc' => ['description' => SORT_ASC],
            'desc' => ['description' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['codigo'] = [
            'asc' => ['codigo' => SORT_ASC],
            'desc' => ['codigo' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['clienteNombre'] = [
            'asc' => ['clienteNombre' => SORT_ASC],
            'desc' => ['clienteNombre' => SORT_DESC],
        ];

       /* $dataProvider->sort->attributes['tipoProducto'] = [
            'asc' => ['tipoproductos.tipo' => SORT_ASC],
            'desc' => ['tipoproductos.tipo' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['cantVenta'] = [
            'asc' => ['cantVenta' => SORT_ASC],
            'desc' => ['cantVenta' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['nombre'] = [
            'asc' => ['productos.nombre' => SORT_ASC],
            'desc' => ['productos.nombre' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['desc'] = [
            'asc' => ['productos.desc' => SORT_ASC],
            'desc' => ['productos.desc' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['codigo'] = [
            'asc' => ['productos.codigo' => SORT_ASC],
            'desc' => ['productos.codigo' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['clienteNombre'] = [
            'asc' => ['clientes.nombre' => SORT_ASC],
            'desc' => ['clientes.nombre' => SORT_DESC],
        ]; */

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['tipoProductoId' => $this->tipoProducto])
            //->andFilterWhere(['like', 'tipoProducto', $this->tipoProducto])
            ->andFilterWhere(['like', 'clienteNombre', $this->clienteNombre])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'area_id', $this->area])
            ->andFilterWhere(['>=', 'fecha_facturada', $this->fechaDesde])
            ->andFilterWhere(['<=', 'fecha_facturada', $this->fechaHasta]); 

       /* $query->andFilterWhere(['like', 'productos.codigo', $this->codigo])
            ->andFilterWhere(['like', 'tipoproductos.tipo', $this->tipoProducto])
            ->andFilterWhere(['like', 'clientes.nombre', $this->clienteNombre])
            ->andFilterWhere(['like', 'productos.nombre', $this->nombre])
            ->andFilterWhere(['like', 'orden_ventas.area_id', $this->area])
            ->andFilterWhere(['>=', 'orden_ventas.fecha_facturada', $this->fechaDesde])
            ->andFilterWhere(['<=', 'orden_ventas.fecha_facturada', $this->fechaHasta]); 
            */

        return $dataProvider;
    }

    public function getModelName() {
        return 'VentasPorClientesSearch';
    }
}
