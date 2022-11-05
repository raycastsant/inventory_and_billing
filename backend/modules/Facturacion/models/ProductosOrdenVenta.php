<?php

namespace backend\modules\Facturacion\models;

use Yii;
use backend\modules\Inventario\models\Producto;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "productos_orden_venta".
 *
 * @property int $id
 * @property int $producto_id
 * @property int $orden_venta_id
 *  * @property int $tipoproducto_id
 * @property int $cantidad
 *
 * @property OrdenVenta $ordenVenta
 * @property Producto $producto
 */
class ProductosOrdenVenta extends \yii\db\ActiveRecord
{
    public $seleccionado;      //Para flexibilizar la gestion del formulario de Devoluciones
    public $producto_id_old;   //Para gestionar cuando se cambie un producto por otro, poder restaurar la cantidad reservada del producto que originalmente estaba
    //public $tipoproducto_id;   //Para guardar el historial de la categoria del producto, por si se cambia la categoria del producto quede guardado el historial de la categoria original para los REPORTES

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productos_orden_venta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'producto_id'], 'required', 'message'=>'Seleccione el producto'], //orden_id
            [[ 'cantidad'], 'required', 'message'=>'Seleccione la cantidad'],
            [[ 'cantidad'], 'default', 'value' => 1],  
            [[ 'cantidad'], 'validateCantidad'], 
            [[ 'cantidad'], 'number'], 
            [[ 'precio'], 'required', 'message'=>'Debe establecer el precio del producto'], 
            [[ 'precio'], 'number','min'=>0, 'message'=>'Debe establecer un precio válido'], 
            [['producto_id', 'orden_venta_id', 'tipoproducto_id'], 'integer'],
            [['seleccionado', 'producto_id_old'], 'safe'],
            [['orden_venta_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdenVenta::className(), 'targetAttribute' => ['orden_venta_id' => 'id']],
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
            'producto_id' => 'Producto ID',
            'orden_venta_id' => 'Orden Venta ID',
            'cantidad' => 'Cantidad',
        ];
    }

    /** Validar que la cantidad de productos establecida no exceda la existencia en almacen
     * o la cantidad reservada. $this->$attribute es la cantidad de productos establecida en el formulario */
    public function validateCantidad($attribute, $params) {
        $prod = $this->getProductoModel();
        if ($this->$attribute < 0.01) {
            $this->addError($attribute, 'La cantidad debe ser mayor que 0');
        }
        else
        if (isset($prod) && ( ((double)$this->$attribute) > ((double)$prod->existencia)) ) {
            $this->addError($attribute, 'La cantidad excede la existencia en almacén');
        }
        else 
        if(isset($prod)) {   //Validar que no se escoja lo que esta reservado
            if($this->isNewRecord) {
                if(($this->$attribute+$prod->cant_reservada) > $prod->existencia)
                    $this->setCantidadAttrErrors($attribute, $prod);
            }
            else {
                $model = $this->getRecordModel($this->id);  //Si se esta editando el mismo registro tengo que verificar rebajando la diferencia del registro que esta reservada
                if( ($this->$attribute + ($prod->cant_reservada - $model->cantidad)) > $prod->existencia)
                    $this->setCantidadAttrErrors($attribute, $prod);
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
    public function getOrdenVenta()
    {
        return $this->hasOne(OrdenVenta::className(), ['id' => 'orden_venta_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto() {
        return $this->hasOne(Producto::className(), ['id' => 'producto_id']);
    }

    /**Devuelve el modelo del producto asignado */
    private function getProductoModel() {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getProducto()]);

       return isset($dataProvider->getModels()[0]) ? $dataProvider->getModels()[0] : null;
    }

    /** Devuelve un modelo ProductosOrdenVenta */
    private function getRecordModel($id) {
        if ( ($model = ProductosOrdenVenta::findOne(['id'=>$id]) ) !== null) {
            return $model;
       }
       throw new NotFoundHttpException('No se encontró el registro para el id:'.$id);
    }
}
