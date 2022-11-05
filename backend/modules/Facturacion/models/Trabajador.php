<?php

namespace backend\modules\Facturacion\models;

use Yii;

/**
 * This is the model class for table "trabajador".
 *
 * @property int $id
 * @property string $nombre
 * @property string $ci
 * @property string $direccion
 * @property string $telefono
 * @property int $eliminado
 *
 * @property ServicioTrabajador[] $servicioTrabajadors
 */
class Trabajador extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trabajador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'ci', 'direccion'], 'required'],
            [['nombre'], 'string', 'max' => 100],
            [['ci'], 'number'],
            [['direccion'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 40],
            [['ci'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre y Apellidos',
            'ci' => 'CI',
            'direccion' => 'Dirección',
            'telefono' => 'Télefono',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicioTrabajadors()
    {
        return $this->hasMany(ServicioTrabajador::className(), ['trabajador_id' => 'id']);
    }
}
