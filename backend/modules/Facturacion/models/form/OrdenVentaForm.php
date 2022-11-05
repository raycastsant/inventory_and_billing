<?php

namespace backend\modules\Facturacion\models\form;

use backend\modules\Facturacion\models\ProductosOrdenVenta;
use backend\modules\Facturacion\models\OrdenVenta;
use backend\modules\Facturacion\models\OrdenSeries;
use backend\modules\Inventario\models\Producto;
use backend\modules\Seguridad\controllers\TrazasVentasController;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;


class OrdenVentaForm extends Model
{
    private $_ordenVenta;
    private $_productosOrdenVentas;

    private $aux_monto;

    public function rules()
    {
        return [
            [['OrdenVenta'], 'required'],
            [['ProductosOrdenVentas'], 'safe'],
        ];
    }

    public function afterValidate()
    {
        if (!Model::validateMultiple($this->getAllModels())) {
            $this->addError(null); // add an empty error to prevent saving
        }
        parent::afterValidate();
    }

    public function saveAll()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $newRecord = $this->ordenVenta->isNewRecord;
        $this->aux_monto = 0;

        if (!$this->ordenVenta->save()) {
            $transaction->rollBack();
            return false;
        }

        $r = false;
        if ($newRecord)
            $r = TrazasVentasController::insert("Nueva orden, código: " . $this->ordenVenta->codigo, $this->ordenVenta->id);
        else
            $r = TrazasVentasController::insert("Actualizar datos de la orden, código: " . $this->ordenVenta->codigo, $this->ordenVenta->id);
        if (!$r) {
            $transaction->rollBack();
            return false;
        }

        //save productos
        if (!$this->saveOrdenProductos()) {
            $transaction->rollBack();
            return false;
        }

        if (!$newRecord && count($this->_productosOrdenVentas) <= 0) {
            $this->ordenVenta->addError('ProductosOrdenVentas', 'Debe insertar al menos un producto');
            $transaction->rollBack();
            return false;
        }

        if ($newRecord) {
            if (($osmodel = OrdenSeries::findOne(['tipo' => 'VENTAS'])) !== null) {
                $osmodel->valor = $osmodel->valor + 1;
                if (!$osmodel->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
        }

        $this->ordenVenta->precio_estimado = $this->aux_monto + $this->ordenVenta->monto_adicional;
        if (!$this->ordenVenta->save()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getOrdenVenta()
    {
        return $this->_ordenVenta;
    }

    public function setOrdenVenta($ordenVenta)
    {
        if ($ordenVenta instanceof OrdenVenta) {
            $this->_ordenVenta = $ordenVenta;
        } else if (is_array($ordenVenta)) {
            $this->_ordenVenta->setAttributes($ordenVenta);
        }
    }

    /*========= Productos ====================*/
    private function saveOrdenProductos()
    {
        $keep = [];
        //$dupFinder = [];
        //$dup;
        foreach ($this->productosOrdenVentas as $prodOrden) {
            $prodOrden->orden_venta_id = $this->ordenVenta->id;

            /*$dup = $prodOrden->producto_id;
            if(array_key_exists($dup, $dupFinder)) {
                $this->ordenVenta->addError("productosOrdenVentas", "Productos repetidos"); 
                return false;
            }*/

            //Reservo el producto
            $cant_a_reservar = $prodOrden->cantidad;

            //Busco si ya este producto tenia reserva en esta Orden para aumentar la diferencia a la cantidad reservada
            if (!$prodOrden->isNewRecord) {

                //Busco el record guardado en BD para modificarlo
                if (($oldRecord = ProductosOrdenVenta::findOne(['id' => $prodOrden->id])) !== null) {

                    //Verifico si se cambió algun producto para reasignar la Reserva
                    if ($prodOrden->producto_id != $prodOrden->producto_id_old) {
                        if (($OLD_Product = Producto::findOne(['id' => $prodOrden->producto_id_old])) !== null) {
                            $restValue = ($OLD_Product->cant_reservada - $oldRecord->cantidad);  //Valor reservado a eliminar del producto original
                            //  $cant_a_reservar += $restValue;   //Al nuevo producto le sumo lo que tenia el original

                            $OLD_Product->cant_reservada = round($restValue, 3);  //elimino lo que tenia reservado en la orden
                            $OLD_Product->save(false);
                        } else {
                            $this->ordenServordenVentaicio->addError("productosOrdenVentas", "Error obteniendo registro de producto ID: " + $prodOrden->producto_id_old . ". Inténtelo nuevamente o contacte al Administrador del sistema");
                            return false;
                        }
                    } else {   //No se cambio ningun producto por otro
                        $cant_a_reservar = ($cant_a_reservar - $oldRecord->cantidad);
                    }
                } else {
                    $this->ordenServordenVentaicio->addError("productosOrdenVentas", "Error obteniendo registro de productos reservados. Contacte al administrador del sistema");
                    return false;
                }
            }

            $prodOrden->producto->cant_reservada = round($prodOrden->producto->cant_reservada + $cant_a_reservar, 3);
            $prodOrden->tipoproducto_id = $prodOrden->producto->tipoproducto->id;

            if (!$prodOrden->save(false) || !$prodOrden->producto->save(false)) {
                return false;
            }

            //$dupFinder[$dup] = $dup;
            $keep[] = $prodOrden->id;

            //campo precio_estimado
            $this->aux_monto += ($prodOrden->cantidad * $prodOrden->precio);
        }

        $query = ProductosOrdenVenta::find()->andWhere(['orden_venta_id' => $this->ordenVenta->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $prodOrden) { //para el update
            //Como se elimina el registro hay que rebajar lo que se habia reservado
            $prodOrden->producto->cant_reservada = round($prodOrden->producto->cant_reservada - $prodOrden->cantidad, 3);
            $prodOrden->producto->save(false);
            $prodOrden->delete();
        }

        return true;
    }

    public function getProductosOrdenVentas()
    {
        if ($this->_productosOrdenVentas === null) {
            $this->_productosOrdenVentas = $this->ordenVenta->isNewRecord ? [] : $this->ordenVenta->productosOrdenVentas;
        }

        return $this->_productosOrdenVentas;
    }

    private function getProductosOrdenVenta($key)
    {
        $productoOrden = $key && strpos($key, 'new') === false ? ProductosOrdenVenta::findOne($key) : false;
        if (!$productoOrden) {
            $productoOrden = new ProductosOrdenVenta();
            $productoOrden->loadDefaultValues();
        }

        return $productoOrden;
    }

    public function setProductosOrdenVentas($productosOrdenVentas)
    {
        unset($productosOrdenVentas['__id__']); // remove the hidden row
        $this->_productosOrdenVentas = [];
        foreach ($productosOrdenVentas as $key => $prodOrden) {
            if (is_array($prodOrden)) {
                $this->_productosOrdenVentas[$key] = $this->getProductosOrdenVenta($key);
                $this->_productosOrdenVentas[$key]->setAttributes($prodOrden);
            } elseif ($prodOrden instanceof ProductosOrdenVenta) {
                $this->_productosOrdenVentas[$prodOrden->id] = $prodOrden;
            }
        }
    }

    public function errorSummary($form)
    {
        $errorLists = [];
        foreach ($this->getAllModels() as $id => $model) {
            $errorList = $form->errorSummary($model, [
                'header' => '<p>Ocurrieron los siguientes errores: <b>' . $id . '</b></p>',
            ]);
            $errorList = str_replace('<li></li>', '', $errorList); // remove the empty error
            $errorLists[] = $errorList;
        }

        return implode('', $errorLists);
    }

    private function getAllModels()
    {
        $models = [
            'OrdenVenta' => $this->ordenVenta,
        ];

        foreach ($this->productosOrdenVentas as $id => $prodOrden) {
            $models['ProductosOrdenVenta.' . $id] = $this->productosOrdenVentas[$id];
        }

        return $models;
    }
}
