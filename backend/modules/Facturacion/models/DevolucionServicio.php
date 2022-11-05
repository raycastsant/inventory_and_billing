<?php

namespace backend\modules\facturacion\models;

use Yii;
use backend\modules\Inventario\models\Producto;

/**
 * This is the model class for table "devolucion_servicios".
 *
 * @property int $id
 * @property int $devolucion_id
 * @property int $producto_id
 * @property int $orden_id
 * @property int $cantidad
 *
 * @property Devolucion $devolucion
 * @property OrdenServicio $orden
 * @property Producto $producto
 */
class DevolucionServicio extends \yii\db\ActiveRecord
{
    public $seleccionado;   //Para el formulario de devoluciones parciales

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'devolucion_servicios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['devolucion_id', 'producto_id', 'orden_id', 'cantidad'], 'required'],
            [['devolucion_id', 'producto_id', 'orden_id'], 'integer'],
            ['cantidad', 'number', 'min' => 0.01],
            ['seleccionado', 'safe'],
            [['devolucion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Devolucion::className(), 'targetAttribute' => ['devolucion_id' => 'id']],
            [['orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenServicio::className(), 'targetAttribute' => ['orden_id' => 'id']],
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
            'devolucion_id' => 'Devolucion ID',
            'producto_id' => 'Producto ID',
            'orden_id' => 'Orden ID',
            'cantidad' => 'Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevolucion()
    {
        return $this->hasOne(Devolucion::className(), ['id' => 'devolucion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrden()
    {
        return $this->hasOne(OrdenServicio::className(), ['id' => 'orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Producto::className(), ['id' => 'producto_id']);
    }
}
