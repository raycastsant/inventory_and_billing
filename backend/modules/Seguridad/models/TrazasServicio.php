<?php

namespace backend\modules\seguridad\models;

use Yii;
use common\models\User;
use backend\modules\facturacion\models\OrdenServicio;

/**
 * This is the model class for table "trazas_servicios".
 *
 * @property int $id
 * @property int $orden_servicio_id
 * @property int $user_id
 * @property string $fecha
 * @property string $descripcion
 *
 * @property OrdenServicio $ordenServicio
 * @property User $user
 */
class TrazasServicio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trazas_servicios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orden_servicio_id', 'user_id', 'fecha', 'descripcion'], 'required'],
            [['orden_servicio_id', 'user_id'], 'integer'],
            [['fecha'], 'safe'],
            [['descripcion'], 'string', 'max' => 200],
            [['orden_servicio_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenServicio::className(), 'targetAttribute' => ['orden_servicio_id' => 'id']],
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
            'orden_servicio_id' => 'Orden Servicio ID',
            'user_id' => 'User ID',
            'fecha' => 'Fecha',
            'descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenServicio()
    {
        return $this->hasOne(OrdenServicio::className(), ['id' => 'orden_servicio_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
