<?php

namespace backend\modules\facturacion\models;

use Yii;

/**
 * This is the model class for table "ceo_info".
 *
 * @property int $id
 * @property string $nombre
 * @property string $ci
 * @property string $cuenta_cuc
 * @property string $cuenta_mn
 * @property string $sucursal
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 * @property string $actividad
 * @property string $regimen
 */
class CeoInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ceo_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'ci', 'cuenta_cuc', 'cuenta_mn', 'sucursal', 'direccion', 'telefono', 'email', 'actividad', 'regimen'], 'required'],
            [['nombre', 'telefono'], 'string', 'max' => 50],
            [['ci'], 'string', 'max' => 11],
            [['cuenta_cuc', 'cuenta_mn', 'email'], 'string', 'max' => 20],
            [['sucursal', 'direccion', 'regimen'], 'string', 'max' => 100],
            [['actividad'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'ci' => 'Ci',
            'cuenta_cuc' => 'Cuenta Cuc',
            'cuenta_mn' => 'Cuenta Mn',
            'sucursal' => 'Sucursal',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'actividad' => 'Actividad',
            'regimen' => 'Regime',
        ];
    }
}
