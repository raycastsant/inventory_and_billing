<?php

namespace backend\modules\Inventario\models;

use Yii;

/**
 * This is the model class for table "tipoproductos".
 *
 * @property int $id
 * @property string $tipo
 *
 * @property Producto[] $productos
 */
class Tipoproducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipoproductos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           /* [['id'], 'required'],
            [['id'], 'integer'],*/
            [['tipo'], 'string', 'max' => 50],
            [['tipo'], 'unique'],
           // [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            //'id' => 'ID',
            'tipo' => 'Nombre de categoría',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductos()
    {
        return $this->hasMany(Producto::className(), ['tipoproducto_id' => 'id']);
    }
}
