<?php

namespace backend\modules\seguridad\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "trazas".
 *
 * @property int $id
 * @property int $user_id
 * @property string $nombre_tabla
 * @property string $fecha
 * @property string $descripcion
 *
 * @property User $user
 */
class Traza extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trazas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'nombre_tabla', 'fecha', 'descripcion'], 'required'],
            [['user_id'], 'integer'],
            [['fecha'], 'safe'],
            [['nombre_tabla'], 'string', 'max' => 25],
            [['descripcion'], 'string', 'max' => 200],
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
            'user_id' => 'User ID',
            'nombre_tabla' => 'Nombre Tabla',
            'fecha' => 'Fecha',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
