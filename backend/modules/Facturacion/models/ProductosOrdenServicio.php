<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Inventario\models\Producto;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;

/**
 * This is the model class for table "productos_orden_servicios".
 *
 * @property int $id
 * @property int $orden_id
 * @property int $producto_id
 * @property int $cant_productos
 * @property int $ejecutada
 * @property string $fecha_ejecutada
 * @property int $existencia
 * @property OrdenServicio $orden
 * @property Producto $producto
 */
class ProductosOrdenServicio extends \yii\db\ActiveRecord
{
    //public $existencia;
    public $producto_id_old;   //Para gestionar cuando se cambie un producto por otro, poder restaurar la cantidad reservada del producto que originalmente estaba

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productos_orden_servicios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'producto_id'], 'required', 'message'=>'Seleccione el producto'], //orden_id
            [[ 'precio'], 'required', 'message'=>'Debe establecer el precio del producto'], 
            [[ 'cant_productos'], 'number'], 
            [[ 'precio'], 'number','min'=>0, 'message'=>'Debe establecer un precio válido'], 
            [[ 'cant_productos'], 'required', 'message'=>'Seleccione la cantidad'], 
            [[ 'cant_productos'], 'default', 'value' => 1], 
            [[ 'cant_productos'], 'validateCantidad'], 
            [['orden_id', 'producto_id', 'ejecutada'], 'integer'],
            [['fecha_ejecutada', 'producto_id_old'], 'safe'],
            [['orden_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenServicio::className(), 'targetAttribute' => ['orden_id' => 'id']],
            [['producto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Producto::className(), 'targetAttribute' => ['producto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orden_id' => 'Orden ID',
            'producto_id' => 'Producto ID',
            'cant_productos' => 'Cant Productos',
            'ejecutada' => 'Ejecutada',
            'fecha_ejecutada' => 'Fecha Ejecutada',
            'precio' => 'Precio',
        ];
    }

    /** Validar que la cantidad de productos establecida no exceda la existencia en almacen
     * o la cantidad reservada. $this->$attribute es la cantidad de productos establecida en el formulario */
    public function validateCantidad($attribute, $params) {
        $prod = $this->getProductoModel();
        if ($this->$attribute < 0.01) {
            $this->addError($attribute, 'La cantidad debe ser mayor o igual que 1');
        }
        else
        if (isset($prod) && ($this->$attribute > $prod->existencia)) {
            $this->addError($attribute, 'La cantidad excede la existencia en almacén');
        }
        else 
        if(isset($prod)) {   //Validar que no se escoja lo que esta reservado
            if($this->isNewRecord) {
                if(($this->$attribute+$prod->cant_reservada) > $prod->existencia) {
                    $this->setCantidadAttrErrors($attribute, $prod);
                }
            }
            else {
                $model = $this->getRecordModel($this->id);  //Si se esta editando el mismo registro tengo que verificar rebajando la diferencia del registro que esta reservada
                if( ($this->$attribute + ($prod->cant_reservada - $model->cant_productos)) > $prod->existencia) {
                    $this->setCantidadAttrErrors($attribute, $prod);
                }
            }    
        }
    }

    private function setCantidadAttrErrors($attribute, $prod) {
        $ventasReserv = $prod->getReservasVentasArray();
        $ventOrdens = null;
        if(count($ventasReserv) > 0) {
            $ventOrdens = "--- VENTAS : ";
            foreach($ventasReserv as $v) {
                $ventOrdens .= '   ('.$v['codigo'].')  '; 
            }
        }
        
        if($ventOrdens != null)
            $this->addError('v',  $ventOrdens);

        $serviciosReserv = $prod->getReservasServiciosArray();
        $servOrdens = null;
        if(count($serviciosReserv) > 0) {
            $servOrdens = "--- SERVICIOS : ";
            foreach($serviciosReserv as $s) {
                $servOrdens .= '   ('.$s['codigo'].')  '; 
            }
        }

        if($servOrdens != null)
            $this->addError('s',  $servOrdens);

        $this->addError('o', 'Órdenes en las que se encuentra:');
        $this->addError($attribute, 'El producto está reservado.');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrden()
    {
        return $this->hasOne(OrdenServicio::className(), ['id' => 'orden_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Producto::className(), ['id' => 'producto_id']);
    }

    /**Devuelve el modelo del producto asignado */
    private function getProductoModel() {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getProducto()]);

       return isset($dataProvider->getModels()[0]) ? $dataProvider->getModels()[0] : null;
    }

    /** Devuelve un modelo ProductosOrdenServicio */
    private function getRecordModel($id) {
        if ( ($model = ProductosOrdenServicio::findOne(['id'=>$id]) ) !== null) {
            return $model;
       }
       throw new NotFoundHttpException('No se encontró el registro para el id:'.$id);
    }
}
