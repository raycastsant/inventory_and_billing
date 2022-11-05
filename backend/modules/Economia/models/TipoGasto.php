<?php

namespace backend\modules\Economia\models;

use Yii;

/**
 * This is the model class for table "tipo_gastos".
 *
 * @property int $id
 * @property string $nombre
 *
 * @property Gasto[] $gastos
 */
class TipoGasto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_gastos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 200],
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
    public function getGastos()
    {
        return $this->hasMany(Gasto::className(), ['tipo_gasto_id' => 'id']);
    }
}
