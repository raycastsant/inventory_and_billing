<?php

namespace backend\modules\seguridad\models;

use Yii;
use common\models\User;
use backend\modules\facturacion\models\OrdenVenta;

/**
 * This is the model class for table "trazas_ventas".
 *
 * @property int $id
 * @property int $orden_venta_id
 * @property int $user_id
 * @property string $fecha
 * @property string $descripcion
 *
 * @property OrdenVenta $ordenVenta
 * @property User $user
 */
class TrazasVenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trazas_ventas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orden_venta_id', 'user_id', 'fecha', 'descripcion'], 'required'],
            [['orden_venta_id', 'user_id'], 'integer'],
            [['fecha'], 'safe'],
            [['descripcion'], 'string', 'max' => 200],
            [['orden_venta_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenVenta::className(), 'targetAttribute' => ['orden_venta_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orden_venta_id' => 'Orden Venta ID',
            'user_id' => 'User ID',
            'fecha' => 'Fecha',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenVenta()
    {
        return $this->hasOne(OrdenVenta::className(), ['id' => 'orden_venta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
