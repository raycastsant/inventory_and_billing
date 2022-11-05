<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Nomencladores\models\Servicio;

/**
 * This is the model class for table "servicio_trabajador".
 *
 * @property int $id
 * @property int $trabajador_id
 * @property int $orden_servicio_id
 * @property int $servicio_id
 *
 * @property OrdenServicio $ordenServicio
 * @property Trabajador $trabajador
 * @property Servicio $servicio
 */
class ServicioTrabajador extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicio_trabajador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trabajador_id', 'servicio_id'], 'required'], //orden_servicio_id
            [[ 'precio'], 'required', 'message'=>'Debe establecer el precio del servicio'], 
            [[ 'precio'], 'number','min'=>0, 'message'=>'Debe establecer un precio válido'], 
            [['trabajador_id', 'orden_servicio_id', 'servicio_id'], 'integer'],
           // [[ 'fecha'], 'required', 'message'=>'Seleccione la fecha'],
           // [['fecha'], 'safe'],
            [['orden_servicio_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenServicio::className(), 'targetAttribute' => ['orden_servicio_id' => 'id']],
            [['trabajador_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trabajador::className(), 'targetAttribute' => ['trabajador_id' => 'id']],
            [['servicio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Servicio::className(), 'targetAttribute' => ['servicio_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trabajador_id' => 'Trabajador ID',
            'orden_servicio_id' => 'Orden Servicio ID',
            'servicio_id' => 'Servicio ID',
          //  'fecha' => 'Fecha',
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
    public function getTrabajador()
    {
        return $this->hasOne(Trabajador::className(), ['id' => 'trabajador_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicio()
    {
        return $this->hasOne(Servicio::className(), ['id' => 'servicio_id']);
    }
}
