<?php

namespace backend\modules\Nomencladores\models;

use Yii;

/**
 * This is the model class for table "estado_orden".
 *
 * @property int $id
 * @property string $estado
 *
 * @property OrdenServicio[] $ordenServicios
 * @property OrdenVenta[] $ordenVentas
 */
class EstadoOrden extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estado_orden';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'required'],
            [['estado'], 'string', 'max' => 40],
            [['estado'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenServicios()
    {
        return $this->hasMany(OrdenServicio::className(), ['estado_orden_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenVentas()
    {
        return $this->hasMany(OrdenVenta::className(), ['estado_orden_id' => 'id']);
    }
}
