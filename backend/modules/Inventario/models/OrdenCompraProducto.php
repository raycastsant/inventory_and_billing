<?php

namespace backend\modules\Inventario\models;

use Yii;

/**
 * This is the model class for table "orden_compra_productos".
 *
 * @property int $id
 * @property int $orden_compra_id
 * @property int $producto_id
 * @property int $cantidad
 *
 * @property OrdenCompra $ordenCompra
 * @property Producto $producto
 */
class OrdenCompraProducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_compra_productos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'producto_id'], 'required', 'message'=>'Seleccione el producto'],
            [[ 'cantidad'], 'required', 'message'=>'Seleccione la cantidad'], 
            [[ 'costo'], 'required', 'message'=>'Seleccione el costo'], 
            [[ 'cantidad'], 'number','min'=>1],
            [[ 'costo'], 'number','min'=>0],
            [[ 'cantidad', 'costo'], 'default', 'value' => 1], 
            [['orden_compra_id', 'producto_id'], 'integer'],
            [['orden_compra_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenCompra::className(), 'targetAttribute' => ['orden_compra_id' => 'id']],
            [['producto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Producto::className(), 'targetAttribute' => ['producto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orden_compra_id' => 'Orden Compra ID',
            'producto_id' => 'Producto ID',
            'cantidad' => 'Cantidad',
            'costo' => 'Costo (cuc)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenCompra()
    {
        return $this->hasOne(OrdenCompra::className(), ['id' => 'orden_compra_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Producto::className(), ['id' => 'producto_id']);
    }
}
