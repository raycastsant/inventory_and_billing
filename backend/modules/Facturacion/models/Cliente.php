<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Nomencladores\models\TipoCliente;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $nombre
 * @property string $codigo
 * @property string $telefono
 * @property string $fax
 * @property string $direccion
 * @property string $email
 * @property int $tipo_cliente_id
 * @property int $eliminado
 *
 * @property TipoCliente $tipoCliente
 * @property OrdenServicio[] $ordenServicios
 * @property OrdenVenta[] $ordenVentas
 * @property Vehiculo[] $vehiculos
 * @property ClienteEmpresaResponsable[] $clienteEmpresaResponsables
 */
class Cliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['tipo_cliente_id', 'required'],
            ['nombre', 'required', 'message'=>'Inserte un nombre'],
            [['tipo_cliente_id'], 'integer'],
            [['nombre'], 'string', 'max' => 56],
            [['telefono'], 'string', 'max' => 40],
            [['fax'], 'string', 'max' => 20],
            [['direccion'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 30],
            [['email'], 'email'],
            [['nombre'], 'unique'],
            [['cid'], 'safe'],
            [['tipo_cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCliente::className(), 'targetAttribute' => ['tipo_cliente_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
          //  'codigo' => 'Codigo',
            'telefono' => 'Teléfono',
            'fax' => 'Fax',
            'direccion' => 'Dirección',
            'tipo_cliente_id' => 'Tipo Cliente',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoCliente()
    {
        return $this->hasOne(TipoCliente::className(), ['id' => 'tipo_cliente_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenServicios()
    {
        return $this->hasMany(OrdenServicio::className(), ['cliente_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenVentas()
    {
        return $this->hasMany(OrdenVenta::className(), ['cliente_id' => 'id'])->orderBy('codigo desc');
    }

    public function getVentasQuery() {
        $query = OrdenVenta::find();
        $query->innerJoin('clientes', 'orden_ventas.cliente_id=clientes.id');
        $query->andWhere('cliente_id='.$this->id);

        return $query;
    }

    public function getServiciosQuery() {
        $query = OrdenServicio::find();
        $query->innerJoin('vehiculos', 'orden_servicios.vehiculo_id=vehiculos.id');
        $query->innerJoin('clientes', 'vehiculos.cliente_id=clientes.id');
        $query->andWhere('cliente_id='.$this->id);

        return  $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehiculos()
    {
        return $this->hasMany(Vehiculo::className(), ['cliente_id' => 'id']);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClienteEmpresaResponsables()
    {
        return $this->hasMany(ClienteEmpresaResponsable::className(), ['cliente_id' => 'id']);
    }
}
