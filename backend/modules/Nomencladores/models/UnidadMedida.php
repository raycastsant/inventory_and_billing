<?php

namespace backend\modules\Nomencladores\models;

use Yii;

/**
 * This is the model class for table "unidad_medida".
 *
 * @property int $id
 * @property string $unidad_medida
 *
 * @property Productos[] $productos
 */
class UnidadMedida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unidad_medida';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unidad_medida'], 'required'],
            [['unidad_medida'], 'string', 'max' => 40],
            [['unidad_medida'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unidad_medida' => 'Unidad Medida',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Productos::className(), ['unidad_medida_id' => 'id']);
    }
}
