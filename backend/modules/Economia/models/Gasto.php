<?php

namespace backend\modules\Economia\models;

use Yii;

/**
 * This is the model class for table "gastos".
 *
 * @property int $id
 * @property int $tipo_gasto_id
 * @property double $cantidad
 * @property string $fecha
 * @property string $descripcion
 *
 * @property TipoGasto $tipoGasto
 */
class Gasto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gastos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_gasto_id', 'cantidad', 'fecha'], 'required'],
            [['tipo_gasto_id'], 'integer'],
            [['cantidad'], 'number'],
            [['fecha'], 'safe'],
            [['descripcion'], 'string', 'max' => 255],
            [['tipo_gasto_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoGasto::className(), 'targetAttribute' => ['tipo_gasto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_gasto_id' => 'Tipo Gasto ID',
            'cantidad' => 'Cantidad',
            'fecha' => 'Fecha',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoGasto()
    {
        return $this->hasOne(TipoGasto::className(), ['id' => 'tipo_gasto_id']);
    }
}
