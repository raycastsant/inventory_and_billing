<?php

namespace backend\modules\facturacion\models;

use Yii;

/**
 * This is the model class for table "cliente_empresa_responsables".
 *
 * @property int $id
 * @property int $cliente_id
 * @property string $nombre
 * @property string $telefono
 * @property string $ci
 * @property string $email
 *
 * @property Cliente $cliente
 */
class ClienteEmpresaResponsable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cliente_empresa_responsables';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required', 'message'=>'Inserte nombre y apellidos'],
            //[['email'], 'required', 'message'=>'Inserte el email'],
            [['telefono'], 'required', 'message'=>'Inserte el teléfono'],
            [['ci'], 'required', 'message'=>'Inserte el CI'],
            [['cliente_id'], 'integer'],
            [['email'], 'string'],
            [['email'], 'email'],
            [['nombre'], 'string', 'max' => 100],
            [['telefono'], 'string', 'max' => 50],
            [['ci'], 'string', 'max' => 11],
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
            'cliente_id' => 'Cliente ID',
            'nombre' => 'Nombre',
            'telefono' => 'Telefono',
            'ci' => 'CI',
            'email' => 'Email',
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
