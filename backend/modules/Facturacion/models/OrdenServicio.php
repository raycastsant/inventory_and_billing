<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Nomencladores\models\EstadoOrden;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Seguridad\models\TrazasServicio;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "orden_servicios".
 *
 * @property int $id
 * @property string $codigo
 * @property int $cliente_id
 * @property int $estado_orden_id
 * @property int $area_id
 * @property string $fecha_iniciada
 * @property string $fecha_facturada
 * @property string $fecha_cobrada
 * @property double $precio_estimado
 * 
 * @property Cliente $cliente
 * @property EstadoOrden $estadoOrden
 * @property Area $area
 * @property ProductosOrdenServicio[] $productosOrdenServicios
 * @property ServicioTrabajador[] $servicioTrabajadors
 * @property TrazasServicio[] $trazasServicios
 * @property boolean $eliminado
 */
class OrdenServicio extends ActiveRecord
{
    public $serie;   //Para poder evitar cuando se crea una orden que el codigo no 'Choque' con otra que se este creando al mismo tiempo

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orden_servicios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'estado_orden_id', 'area_id', 'fecha_iniciada'], 'required'], //precio_estimado    cliente_id
            [['cliente_id'], 'required', 'message' => 'Debe seleccionar un cliente'],
            [['cliente_id', 'estado_orden_id', 'area_id'], 'integer'],
            [['fecha_iniciada', 'fecha_cobrada', 'fecha_facturada', 'serie'], 'safe'],
            [['codigo'], 'string', 'max' => 40],
            [['codigo'], 'unique'],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::class, 'targetAttribute' => ['cliente_id' => 'id']],
            [['estado_orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadoOrden::class, 'targetAttribute' => ['estado_orden_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::class, 'targetAttribute' => ['area_id' => 'id']],
            [['moneda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Moneda::class, 'targetAttribute' => ['moneda_id' => 'id']],
        ];
    }

    public function saveCreate()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'cliente_id' => 'Cliente',
            'estado_orden_id' => 'Estado Orden ID',
            'area_id' => 'Área',
            'fecha_iniciada' => 'Fecha Iniciada',
            'fecha_facturada' => 'Fecha Facturada',
            'fecha_cobrada' => 'Fecha Cobrada',
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Cliente::class, ['id' => 'cliente_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoOrden()
    {
        return $this->hasOne(EstadoOrden::class, ['id' => 'estado_orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::class, ['id' => 'area_id']);
    }

    public function getMoneda()
    {
        return $this->hasOne(Moneda::class, ['id' => 'moneda_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosOrdenServicios()
    {
        return $this->hasMany(ProductosOrdenServicio::class, ['orden_id' => 'id']);
    }

    /**Devuelve un arreglo de ProductosOrdenServicio*/
    public function getProductosList()
    {
        return ProductosOrdenServicio::find()->andWhere(['orden_id' => $this->id])->all();
    }

    /**Devuelve un ProductosOrdenServicio*/
    public function getProductoOrden($productoId)
    {
        $result =  ProductosOrdenServicio::find()
            ->andWhere(['orden_id' => $this->id])
            ->andWhere(['producto_id' => $productoId])
            ->all();
        if (count($result) > 0)
            return $result[0];
        else
            return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicioTrabajadors()
    {
        return $this->hasMany(ServicioTrabajador::class, ['orden_servicio_id' => 'id']);
    }

    /**Devuelve los servicios agrupados por nombreServicio - sumaPrecio, sin el trabajador */
    public function getServicios()
    {
        $query = ServicioTrabajador::find()->select(['servicios.nombre', 'sum(precio) as precio', 'servicio_id'])->where(['orden_servicio_id' => $this->id])
            ->groupBy('servicios.nombre');
        $query->joinWith(['servicio']);
        return $query->asArray()->all();
    }

    /**Devuelve los servicios agrupados por nombreServicio - sumaPrecio, sin el trabajador */
    public function getMontoServicios()
    {
        $sum = ServicioTrabajador::find()->where(['orden_servicio_id' => $this->id])->sum('precio');
        return $sum;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrazasServicios()
    {
        return $this->hasMany(TrazasServicio::class, ['orden_servicio_id' => 'id']);
    }
}
