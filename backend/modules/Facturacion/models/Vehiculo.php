<?php

namespace backend\modules\facturacion\models;

use Yii;


/**
 * This is the model class for table "vehiculos".
 *
 * @property int $id
 * @property int $cliente_id
 * @property string $chapa
 * @property string $modelo
 * @property string $descripcion
 *
 * @property Cliente $cliente
 */
class Vehiculo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehiculos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['cliente_id'], 'required'],   ***Para que pueda guardarse, porque en el ClienteForm primero se llama al validate(), y luego es que se setea el id del cliente a cada vehiculo 
            [[ 'marca'], 'required', 'message'=>'Inserte la marca'], 
            [[ 'modelo'], 'required', 'message'=>'Inserte el modelo'], 
            [['cliente_id', 'anno'], 'integer'],
            [['chapa'], 'string', 'max' => 20],
            [['modelo', 'marca', 'codigo_motor', 'codigo_alternador'], 'string', 'max' => 50],
            [['fabricante'], 'string', 'max' => 150],
           // [['chapa'], 'unique', 'message'=>'La matrícula insertada ya existe en el sistema'],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['cliente_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cliente_id' => 'Cliente',
            'chapa' => 'Matrícula',
            'modelo' => 'Modelo',
            'fabricante' => 'Fabricante',
            'codigo_motor' => 'Código motor',
            'codigo_alternador' => 'Código alternador',
            'anno' => 'Año',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::className(), ['id' => 'cliente_id']);
    }
}
