<?php

namespace backend\modules\facturacion\models;

use Yii;

/**
 * This is the model class for table "monedas".
 *
 * @property int $id
 * @property string $nombre
 *
 * @property MonedaCambio[] $monedaCambios
 * @property MonedaCambio[] $monedaCambios0
 */
class Moneda extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monedas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            ['nombre', 'unique', 'message'=>'La moneda ya existe'],
            [['nombre'], 'string', 'max' => 20],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonedaCambios()
    {
        return $this->hasMany(MonedaCambio::className(), ['m1_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonedaCambios0()
    {
        return $this->hasMany(MonedaCambio::className(), ['m2_id' => 'id']);
    }

    /**Busca el valor de la tasa de cambio respecto a la moneda 2 */
    public function findCambioMoneda($moneda2Id) {
        foreach($this->monedaCambios as $mc) {
            if($mc->m1_id = $this->id && $mc->m2_id = $moneda2Id) {
                return $mc;
            }
        }

        return null;
    }
}
