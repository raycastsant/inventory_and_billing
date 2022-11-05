<?php

namespace backend\modules\Inventario\models;

use Yii;
use backend\modules\facturacion\models\Moneda;

/**
 * This is the model class for table "orden_compra".
 *
 * @property int $id
 * @property string $fecha_creada
 * @property string $codigo
 *
 * @property OrdenCompraProducto[] $ordenCompraProductos
 */
class OrdenCompra extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_compra';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['fecha_creada', 'required'],
            ['codigo', 'required', 'message'=>'Inserte el código'],
            ['moneda_id', 'integer'],
            [['fecha_creada'], 'safe'],
            [['codigo'], 'string', 'max' => 50],
            [['codigo'], 'unique', 'message'=>'El código de la orden ya existe'],
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
            'fecha_creada' => 'Fecha Creada',
            'codigo' => 'Código',
        ];
    }

    public function getMoneda()
    {
        return $this->hasOne(Moneda::className(), ['id' => 'moneda_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenCompraProductos()
    {
        return $this->hasMany(OrdenCompraProducto::className(), ['orden_compra_id' => 'id']);
    }
}
