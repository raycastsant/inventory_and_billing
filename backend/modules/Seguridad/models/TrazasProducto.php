<?php

namespace backend\modules\seguridad\models;

use Yii;
use common\models\User;
use backend\modules\inventario\models\Producto;

/**
 * This is the model class for table "trazas_productos".
 *
 * @property int $id
 * @property int $producto_id
 * @property int $user_id
 * @property string $fecha
 * @property string $descripcion
 *
 * @property Producto $producto
 * @property User $user
 */
class TrazasProducto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trazas_productos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['producto_id', 'user_id', 'fecha', 'descripcion'], 'required'],
            [['producto_id', 'user_id'], 'integer'],
            [['fecha'], 'safe'],
            [['descripcion'], 'string', 'max' => 200],
            [['producto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Producto::className(), 'targetAttribute' => ['producto_id' => 'id']],
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
            'producto_id' => 'Producto ID',
            'user_id' => 'User ID',
            'fecha' => 'Fecha',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Producto::className(), ['id' => 'producto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
