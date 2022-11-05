<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Nomencladores\models\EstadoOrden;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Seguridad\models\TrazasServicio;
use \yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
 * @property Vehiculo $vehiculo
 * @property EstadoOrden $estadoOrden
 * @property Area $area
 * @property ProductosOrdenServicio[] $productosOrdenServicios
 * @property ServicioTrabajador[] $servicioTrabajadors
 * @property TrazasServicio[] $trazasServicios
 * @property boolean $eliminado
 */
class OrdenServicio extends ActiveRecord
{
    public $cliente;
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
            [[ 'vehiculo_id'], 'required', 'message'=>'Debe seleccionar un vehículo'], 
            [['vehiculo_id', 'estado_orden_id', 'area_id'], 'integer'],
            [['fecha_iniciada', 'fecha_cobrada', 'fecha_facturada', 'serie'], 'safe'],
           // [['precio_estimado'], 'number'],
            [['codigo'], 'string', 'max' => 40],
            [['codigo'], 'unique'],
           // [['servicioTrabajadors'], 'validateServicios'],
            [['vehiculo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehiculo::className(), 'targetAttribute' => ['vehiculo_id' => 'id']],
            //[['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cliente::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['estado_orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadoOrden::className(), 'targetAttribute' => ['estado_orden_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['moneda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Moneda::className(), 'targetAttribute' => ['moneda_id' => 'id']],
        ];
    }

    /*public function behaviors() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'actualizarSerie',
        ];
    }

    public function actualizarSerie($event) {
        if ( ($model = OrdenSeries::findOne(['tipo'=>'SERVICIOS']) ) !== null) {
            $model->valor =  2; //$model->valor+1;
            $model->save();
        }
        $this->codigo = 'dfhlgsdflhgdf';
        $this->save();
    }*/

    public function saveCreate() {

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'vehiculo_id' => 'Vehiculo',
            'estado_orden_id' => 'Estado Orden ID',
            'area_id' => 'Área',
            'fecha_iniciada' => 'Fecha Iniciada',
            'fecha_facturada' => 'Fecha Facturada',
            'fecha_cobrada' => 'Fecha Cobrada',
        ];
    }

  /*  public function validateServicios() {
        //if (array_count_values($this->servicioTrabajadors) <= 0) {
            $this->addError('servicioTrabajadors', 'Debe insertar al menos un servicio');
            return false;
        //}
        
    }*/
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente() {
        return $this->getVehiculo()->getCliente();

       // return $this->hasOne(Cliente::className(), ['id' => 'cliente_id']);
    }

    public function getVehiculo()
    {
        return $this->hasOne(Vehiculo::className(), ['id' => 'vehiculo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoOrden()
    {
        return $this->hasOne(EstadoOrden::className(), ['id' => 'estado_orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    public function getMoneda()
    {
        return $this->hasOne(Moneda::className(), ['id' => 'moneda_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosOrdenServicios()
    {
        return $this->hasMany(ProductosOrdenServicio::className(), ['orden_id' => 'id']);
    }

     /**Devuelve un arreglo de ProductosOrdenServicio*/
    public function getProductosList() {
        return ProductosOrdenServicio::find()->andWhere(['orden_id' => $this->id])->all();
    }

    /**Devuelve un ProductosOrdenServicio*/
    public function getProductoOrden($productoId) {
        $result =  ProductosOrdenServicio::find()
                ->andWhere(['orden_id' => $this->id])
                ->andWhere(['producto_id' => $productoId])
                ->all();
        if(count($result) > 0)
            return $result[0];
        else
            return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicioTrabajadors()
    {
        return $this->hasMany(ServicioTrabajador::className(), ['orden_servicio_id' => 'id']);
    }

    /**Devuelve los servicios agrupados por nombreServicio - sumaPrecio, sin el trabajador */
    public function getServicios() {
        $query = ServicioTrabajador::find()->select(['servicios.nombre', 'sum(precio) as precio', 'servicio_id'])->where(['orden_servicio_id' => $this->id])
                 ->groupBy('servicios.nombre');
                    $query->joinWith(['servicio']);
        return $query->asArray()->all();
    }

     /**Devuelve los servicios agrupados por nombreServicio - sumaPrecio, sin el trabajador */
     public function getMontoServicios() {
        $sum = ServicioTrabajador::find()->where(['orden_servicio_id' => $this->id])->sum('precio');
        return $sum;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrazasServicios()
    {
        return $this->hasMany(TrazasServicio::className(), ['orden_servicio_id' => 'id']);
    }
}
