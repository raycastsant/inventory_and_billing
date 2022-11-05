<?php

namespace backend\modules\Facturacion\models;

use backend\modules\Nomencladores\models\EstadoOrden;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Seguridad\models\TrazasVenta;
use Yii;

/**
 * This is the model class for table "orden_ventas".
 *
 * @property int $id
 * @property string $codigo
 * @property int $cliente_id
 * @property int $estado_orden_id
 * @property int $area_id
 * @property string $fecha_iniciada
 * @property string $fecha_facturada
 * @property string $fecha_cobrada
 *
 * @property Cliente $cliente
 * @property Vehiculo $vehiculo
 * @property EstadoOrden $estadoOrden
 * @property Area $area
 * @property ProductosOrdenVenta[] $productosOrdenVentas
 * @property TrazasVenta[] $trazasVentas
 * @property boolean $eliminado
 */
class OrdenVenta extends \yii\db\ActiveRecord
{
    public $serie;   //Para poder evitar cuando se crea una orden que el codigo no 'Choque' con otra que se este creando al mismo tiempo

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_ventas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'estado_orden_id', 'area_id', 'fecha_iniciada'], 'required'],    //, 'fecha_cerrada'
            [['cliente_id'], 'required', 'message'=>'Debe seleccionar el cliente'],  
            [['cliente_id', 'estado_orden_id', 'area_id', 'vehiculo_id'], 'integer'],
            [['fecha_iniciada', 'fecha_cobrada', 'fecha_facturada', 'serie'], 'safe'],
            [['codigo'], 'string', 'max' => 40],
            [['codigo'], 'unique', 'message'=>'Ya existe una orden con el código insertado'],
            ['monto_adicional', 'number', 'min' => 0],
            ['monto_adicional_desc', 'string', 'max' => 200],
            [['vehiculo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehiculo::className(), 'targetAttribute' => ['vehiculo_id' => 'id']],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['estado_orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadoOrden::className(), 'targetAttribute' => ['estado_orden_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['moneda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Moneda::className(), 'targetAttribute' => ['moneda_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'cliente_id' => 'Cliente',
            'estado_orden_id' => 'Estado',
            'area_id' => 'Área',
            'fecha_iniciada' => 'Fecha Iniciada',
            'fecha_facturada' => 'Fecha Facturada',
            'fecha_cobrada' => 'Fecha Cobrada',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['id' => 'cliente_id']);
    }

    public function getVehiculo()
    {
        return $this->hasOne(Vehiculo::className(), ['id' => 'vehiculo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoOrden()
    {
        return $this->hasOne(EstadoOrden::className(), ['id' => 'estado_orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    public function getMoneda()
    {
        return $this->hasOne(Moneda::className(), ['id' => 'moneda_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosOrdenVentas()
    {
        return $this->hasMany(ProductosOrdenVenta::className(), ['orden_venta_id' => 'id']);
    }

    /**Devuelve un arreglo de ProductosOrdenVenta*/
    public function getProductosList() {
        return ProductosOrdenVenta::find()->andWhere(['orden_venta_id' => $this->id])->all();
    }

    /**Devuelve un ProductosOrdenVenta*/
    public function getProductoOrden($productoId) {
        $result =  ProductosOrdenVenta::find()
                ->andWhere(['orden_venta_id' => $this->id])
                ->andWhere(['producto_id' => $productoId])
                ->all();
        if(count($result) > 0)
            return $result[0];
        else
            return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrazasVentas()
    {
        return $this->hasMany(TrazasVenta::className(), ['orden_venta_id' => 'id']);
    }
}
