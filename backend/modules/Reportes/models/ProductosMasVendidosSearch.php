<?php

namespace backend\modules\Reportes\models;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Producto;


class ProductosMasVendidosSearch extends Producto
{
    //private $criterio = 2;
    public $myPageSize;
    public $tipoProducto;
    public $ordenCod;
    public $cantVenta = 2;
    public $fechaDesde;
    public $fechaHasta;
    public $area;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipoproducto_id', 'cantVenta'], 'integer'],
            ['cantVenta', 'default', 'value' => 2],
            [['nombre', 'codigo', 'desc', 'desc_ampliada', 'ordenCod', 'tipoProducto', 'cantVenta', 'fechaDesde', 'fechaHasta', 'area', 'myPageSize'], 'safe'],
            ['fechaDesde', 'validateFechaDesde'],
            [['costo', 'precio'], 'number'],
        ];
    }

    public function validateFechaDesde($attribute, $params, $validator)
    {
        if ($this->fechaHasta != null && strtotime($this->$attribute) > strtotime($this->fechaHasta))
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
    public function search($params)
    {
        //!Importante, debe ir antes de la consulta!
        $this->load($params);

        if (!$this->cantVenta || $this->cantVenta < 0)
            $this->cantVenta = 0;

        /**Se buscan los productos que tengan una cantidad de ventas estimadas, sin tener en cuenta 
         * la existencia en almacen, siempre y cuando la orden correspondiente este FACTURADA, no 'Abierta' ni 'Cancelada'
         */
        /* $query = new \yii\db\Query();  
        
        //TODO Luego de un tiempo esto se puede quitar. Es para dar solucion en el momento en que surgio el tema de eliminar las categorias
            $unnatended = $query->select(['productos_orden_venta.id', 'productos.tipoproducto_id'])
                                ->from('productos_orden_venta')
                                ->innerJoin('productos', 'productos.id=productos_orden_venta.producto_id')
                                ->where('productos_orden_venta.tipoproducto_id is NULL')->all();
            //print_r($unnatended);
            foreach($unnatended as $item) {
                Yii::$app->db->createCommand("update productos_orden_venta set tipoproducto_id=".$item['tipoproducto_id'].
                                             " where id=".$item['id'])->execute();
            }*/

        $query = new \yii\db\Query();
        $query->select([
            'productos.codigo', 'productos.nombre', 'productos.nombre_imagen', 'productos.id', 'productos.desc', 'tipoproductos.tipo as tipoProducto',
            'sum(productos_orden_venta.cantidad) as cantVenta'
        ]);
        $query->from('productos');
        $query->innerJoin('productos_orden_venta', 'productos.id=productos_orden_venta.producto_id');
        //$query->innerJoin('tipoproductos', 'productos_orden_venta.tipoproducto_id=tipoproductos.id');  
        $query->innerJoin('tipoproductos', 'productos.tipoproducto_id=tipoproductos.id');
        $query->innerJoin('orden_ventas', 'productos_orden_venta.orden_venta_id=orden_ventas.id');
        //$query->innerJoin('areas', 'orden_ventas.area_id=areas.id');

        $areaWhere = "";
        if (isset($this->area) && !empty($this->area))
            $areaWhere = " and orden_ventas.area_id=" . $this->area;

        $query->andWhere('orden_ventas.estado_orden_id >= 3 AND orden_ventas.eliminado=false ' . $areaWhere);

        /* $query->andWhere('productos.id in (SELECT productos.id FROM 
                                                  productos INNER JOIN productos_orden_venta ON productos.id=productos_orden_venta.producto_id 
                                                  INNER JOIN orden_ventas ON productos_orden_venta.orden_venta_id=orden_ventas.id
                                                  WHERE (orden_ventas.estado_orden_id >= 3) '.$areaWhere.' GROUP BY productos.id having sum(productos_orden_venta.cantidad)>='.$this->cantVenta.')');*/
        $query->groupBy(['tipoProducto', 'productos.codigo', 'productos.nombre']);
        $query->having('sum(productos_orden_venta.cantidad) >= ' . $this->cantVenta);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        $dataProvider->sort->attributes['tipoProducto'] = [
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
        $dataProvider->sort->attributes['ordenCod'] = [
            'asc' => ['orden_ventas.codigo' => SORT_ASC],
            'desc' => ['orden_ventas.codigo' => SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder['cantVenta'] = SORT_DESC;

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'productos.codigo', $this->codigo])
            ->andFilterWhere(['productos_orden_venta.tipoproducto_id' => $this->tipoProducto])
            //->andFilterWhere(['like', 'tipoproductos.tipo', $this->tipoProducto])
            ->andFilterWhere(['like', 'orden_ventas.codigo', $this->ordenCod])
            ->andFilterWhere(['like', 'productos.nombre', $this->nombre])
            ->andFilterWhere(['like', 'orden_ventas.area_id', $this->area])
            ->andFilterWhere(['>=', 'orden_ventas.fecha_facturada', $this->fechaDesde])
            ->andFilterWhere(['<=', 'orden_ventas.fecha_facturada', $this->fechaHasta]);

        return $dataProvider; // $query->all();
    }

    public function getModelName()
    {
        return 'ProductosMasVendidosSearch';
    }
}
