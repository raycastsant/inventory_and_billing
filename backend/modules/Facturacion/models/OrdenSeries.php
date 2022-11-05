<?php

namespace backend\modules\Facturacion\models;

/**
 * @property string $tipo
 * @property int $valor
 */
class OrdenSeries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_series';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo', 'valor'], 'required'],
            [['valor'], 'integer'],
            [['tipo'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipo' => 'tipo',
            'valor' => 'valor',
        ];
    }
}
