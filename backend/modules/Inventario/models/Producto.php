<?php

namespace backend\modules\Inventario\models;

use Yii;
use backend\modules\Nomencladores\models\UnidadMedida;
use backend\modules\Facturacion\models\ProductosOrdenVenta;
use backend\modules\Facturacion\models\OrdenServicio;
use backend\modules\Facturacion\models\OrdenVenta;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "productos".
 *
 * @property int $id
 * @property int $tipoproducto_id
 * @property int $unidad_medida_id
 * @property string $nombre
 * @property double $costo
 * @property double $precio
 * @property string $codigo
 * @property string $desc
 * @property int $existencia
 * @property string $desc_ampliada
 * @property string $nombre_imagen
 * @property boolean $eliminado
 *
 * @property Tipoproducto $tipoproducto
 * @property UnidadMedida $unidadMedida
 * @property ProductosOrdenServicio[] $productosOrdenServicios
 * @property ProductosOrdenVenta[] $productosOrdenVenta
 */
class Producto extends \yii\db\ActiveRecord
{
    public $imagefile;
    public $trazacambio;

   //Para los reportes 
    public $ordenCod;
    public $ordenId;
    public $tipoProducto;
    public $cantVenta;
    public $clienteId;
    public $clienteNombre;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipoproducto_id', 'unidad_medida_id'], 'required'],
            ['nombre', 'required', 'message'=>'Inserte un nombre'],
            ['codigo', 'required', 'message'=>'Inserte el código'],
            ['stock_minimo', 'required', 'message'=>'Inserte la cantidad mínima'],
            [['tipoproducto_id', 'unidad_medida_id', 'stock_minimo'], 'integer'],
            [['costo', 'precio', 'cant_reservada', 'existencia'], 'number', 'min'=>0],
            ['precio', 'validate_precio'],
            [['desc', 'desc_ampliada'], 'string'],
            ['existencia', 'validate_existencia'],
            ['stock_minimo', 'validate_stock'],
            [['nombre'], 'string', 'max' => 255],
            [['codigo', 'nombre_imagen'], 'string', 'max' => 50],
            [['codigo'], 'unique', 'message'=>'El código insertado ya existe'],
            [['imagefile'], 'file'],
            [['trazacambio', 'ordenCod', 'cantVenta', 'tipoProducto', 'ordenId', 'clienteId', 'clienteNombre'], 'safe'],
            [['tipoproducto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tipoproducto::className(), 'targetAttribute' => ['tipoproducto_id' => 'id']],
            [['unidad_medida_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadMedida::className(), 'targetAttribute' => ['unidad_medida_id' => 'id']],
            //[['almacen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Almacen::className(), 'targetAttribute' => ['almacen_id' => 'id']],
        ];
    }

    public function validate_existencia($attribute, $params, $validator) {
        if($this->$attribute < 0)
            $validator->addError($this, $attribute, 'La existencia debe ser mayor que 0');
    }

    public function validate_stock($attribute, $params, $validator) {
        if($this->$attribute <= 0)
            $validator->addError($this, $attribute, 'La cantidad mínima debe ser mayor que 0');
    }

    public function validate_precio($attribute, $params, $validator) {
        // ($this->$attribute!=0 && $this->costo!=0) && 
        if($this->$attribute<=$this->costo)
            $validator->addError($this, $attribute, 'El precio de venta de ser mayor que el costo');
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipoproducto_id' => 'Tipoproducto ID',
            //'almacen_id' => 'Almacén',
            'unidad_medida_id' => 'Unidad Medida ID',
            'nombre' => 'Nombre',
            'costo' => 'Costo (cup)',
            'precio' => 'Precio (cup)',
            'codigo' => 'Código',
            'desc' => 'Descripción',
            'existencia' => 'Existencia',
            'stock_minimo' => 'Mínimo requerido en almacén',
            'desc_ampliada' => 'Descripción ampliada',
            'nombre_imagen' => 'Nombre Imagen',
            'imagefile' => 'Imagen',
            'stock_minimo' => 'Cantidad mínima en almacén'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoproducto()
    {
        return $this->hasOne(Tipoproducto::className(), ['id' => 'tipoproducto_id']);
    }

    public function Desc() {
        return 'desc';
    }

    /**
     * @return \\yii\db\ActiveRecord
     */
   /* public function getTipoproducto_Record() {
        $tipoid = $this->attributes['tipoproducto_id'];
        if (($model = Tipoproducto::findOne($tipoid)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidadMedida()
    {
        return $this->hasOne(UnidadMedida::className(), ['id' => 'unidad_medida_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
   /* public function getAlmacen()
    {
        return $this->hasOne(Almacen::className(), ['id' => 'almacen_id']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosOrdenServicios()
    {
        return $this->hasMany(ProductosOrdenServicio::className(), ['producto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosOrdenVenta()
    {
        return $this->hasMany(ProductosOrdenVenta::className(), ['producto_id' => 'id']);
    }

    public function getReservasServiciosQuery() {
        $query = OrdenServicio::find();
        $query->select(['orden_servicios.id', 'orden_servicios.codigo', 'productos_orden_servicios.cant_productos']);
        $query->innerJoin('productos_orden_servicios', 'orden_servicios.id=productos_orden_servicios.orden_id');
       // $query->innerJoin('productos', 'productos.id=productos_orden_servicios.producto_id');
        $query->andWhere('productos_orden_servicios.producto_id='.$this->id);
        $query->andWhere('orden_servicios.estado_orden_id = 1');
        $query->andWhere('orden_servicios.eliminado = 0');

        return $query;
    }

    private function getReservasServiciosQuerys() {
        $query = OrdenServicio::find();
        
        $query->innerJoin('productos_orden_servicios', 'orden_servicios.id=productos_orden_servicios.orden_id');
        $query->innerJoin('productos', 'productos.id=productos_orden_servicios.producto_id');
        $query->andWhere('productos_orden_servicios.producto_id='.$this->id);
        $query->andWhere('orden_servicios.estado_orden_id = 1');
        $query->andWhere('orden_servicios.eliminado = 0');

        return $query;
    }

    /** Devuelve un ActiveDataProvider con las ordenes 
     * de Servicio que tienen este Producto reservado*/
    public function getServiciosProvider() {
        $query = $this->getReservasServiciosQuerys();

        return  new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);
    }

    /** Devuelve un Array con las ordenes 
     * de Servicio que tienen este Producto reservado*/
    public function getReservasServiciosArray() {
        return $this->getReservasServiciosQuery()->asArray()->all();
    }

    public function getReservasVentasQuery() {
        $query = OrdenVenta::find();
        $query->select(['orden_ventas.id', 'orden_ventas.codigo', 'sum(productos_orden_venta.cantidad) as cantidad']);
        $query->innerJoin('productos_orden_venta', 'orden_ventas.id=productos_orden_venta.orden_venta_id');
        $query->andWhere('productos_orden_venta.producto_id='.$this->id);
        $query->andWhere('orden_ventas.estado_orden_id = 1');
        $query->andWhere('orden_ventas.eliminado = 0');
        $query->groupBy('orden_ventas.id');

        return $query;
    }

    private function getReservasVentasQuerys() {
        $query = OrdenVenta::find();
        $query->innerJoin('productos_orden_venta', 'orden_ventas.id=productos_orden_venta.orden_venta_id');
        $query->andWhere('productos_orden_venta.producto_id='.$this->id);
        $query->andWhere('orden_ventas.estado_orden_id = 1');
        $query->andWhere('orden_ventas.eliminado = 0');

        return $query;
    }

    /** Devuelve un ActiveDataProvider con las ordenes 
     * de Venta que tienen este Producto reservado*/
    public function getVentasProvider() {
        $query = $this->getReservasVentasQuerys();

        return  new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 8,
            ],
        ]);
    }

    /** Devuelve un Array con las ordenes 
     * de Venta que tienen este Producto reservado*/
    public function getReservasVentasArray() {
        return $this->getReservasVentasQuery()->asArray()->all();
    }
}
