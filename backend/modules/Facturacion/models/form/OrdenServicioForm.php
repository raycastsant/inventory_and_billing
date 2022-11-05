<?php

namespace backend\modules\Facturacion\models\form;

use backend\modules\Facturacion\models\ProductosOrdenServicio;
use backend\modules\Facturacion\models\OrdenServicio;
use backend\modules\Facturacion\models\ServicioTrabajador;
use backend\modules\Inventario\models\Producto;
use backend\modules\Facturacion\models\OrdenSeries;
use backend\modules\Seguridad\controllers\TrazasServiciosController;
use Yii;
use yii\base\Model;


class OrdenServicioForm extends Model
{
    private $_ordenServicio;
    private $_productosOrdenServicios;
    private $_servicioTrabajadors;

    private $aux_monto;

    public function rules()
    {
        return [
            [['OrdenServicio'], 'required'],
            [['ProductosOrdenServicios', 'ServicioTrabajadors'], 'safe'],
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

        $newRecord = $this->ordenServicio->isNewRecord;
        $this->aux_monto = 0;

        if (!$this->ordenServicio->save()) {
            $transaction->rollBack();
            return false;
        }

        $r = false;
        if ($newRecord)
            $r = TrazasServiciosController::insert("Nueva orden, código: " . $this->ordenServicio->codigo, $this->ordenServicio->id);
        else
            $r = TrazasServiciosController::insert("Actualizar datos de la orden, código: " . $this->ordenServicio->codigo, $this->ordenServicio->id);
        if (!$r) {
            $transaction->rollBack();
            return false;
        }

        //save productos
        if (!$this->saveOrdenProductos()) {
            $transaction->rollBack();
            return false;
        }

        //save servicios - trabajador
        if (!$this->saveOrdenServicios()) {
            $transaction->rollBack();
            return false;
        }

        //Validar que se inserte al menos un servicio
        if (!$newRecord && count($this->_servicioTrabajadors) <= 0) {
            $this->ordenServicio->addError('ServicioTrabajadors', 'Debe insertar al menos un servicio');
            $transaction->rollBack();
            return false;
        }

        if ($newRecord) {
            if (($osmodel = OrdenSeries::findOne(['tipo' => 'SERVICIOS'])) !== null) {
                $osmodel->valor = $osmodel->valor + 1;
                if (!$osmodel->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
        }

        $this->ordenServicio->precio_estimado = $this->aux_monto;
        if (!$this->ordenServicio->save()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function getOrdenServicio()
    {
        return $this->_ordenServicio;
    }

    public function setOrdenServicio($ordenServicio)
    {
        if ($ordenServicio instanceof OrdenServicio) {
            $this->_ordenServicio = $ordenServicio;
        } else if (is_array($ordenServicio)) {
            $this->_ordenServicio->setAttributes($ordenServicio);
        }
    }

    /*========= Productos ====================*/
    private function saveOrdenProductos()
    {
        $keep = [];
        $dupFinder = [];
        $dup;
        foreach ($this->productosOrdenServicios as $prodOrden) {
            $prodOrden->orden_id = $this->ordenServicio->id;

            $dup = $prodOrden->producto_id;
            if (array_key_exists($dup, $dupFinder)) {
                $this->ordenServicio->addError("productosOrdenServicios", "Productos duplicados");
                return false;
            }

            //Reservo el producto
            $cant_a_reservar = $prodOrden->cant_productos;

            //Busco si ya este producto tenia reserva en esta Orden para aumentar la diferencia a la cantidad reservada
            if (!$prodOrden->isNewRecord) {

                if (($oldRecord = ProductosOrdenServicio::findOne(['id' => $prodOrden->id])) !== null) {

                    //Verifico si se cambió algun producto para reasignar la Reserva
                    if ($prodOrden->producto_id != $prodOrden->producto_id_old) {
                        if (($OLD_Product = Producto::findOne(['id' => $prodOrden->producto_id_old])) !== null) {
                            $restValue = ($OLD_Product->cant_reservada - $oldRecord->cant_productos);  //Valor reservado a eliminar del producto original
                            $OLD_Product->cant_reservada = round($restValue, 3);  //elimino lo que tenia reservado en la orden
                            $OLD_Product->save(false);
                        } else {
                            $this->ordenServordenVentaicio->addError("productosOrdenVentas", "Error obteniendo registro de producto ID: " + $prodOrden->producto_id_old . ". Inténtelo nuevamente o contacte al Administrador del sistema");
                            return false;
                        }
                    } else {   //No se cambio ningun producto por otro
                        $cant_a_reservar = ($cant_a_reservar - $oldRecord->cant_productos);
                    }
                    //$cant_a_reservar = ($cant_a_reservar-$oldRecord->cant_productos);
                } else {
                    $this->ordenServicio->addError("productosOrdenServicios", "Error obteniendo registro de productos reservados. Contacte al administrador del sistema");
                    return false;
                }
            }
            $prodOrden->producto->cant_reservada = ($prodOrden->producto->cant_reservada + $cant_a_reservar);

            if (!$prodOrden->save(false) || !$prodOrden->producto->save(false)) {
                return false;
            }
            $dupFinder[$dup] = $dup;
            $keep[] = $prodOrden->id;

            //campo precio_estimado
            $this->aux_monto += ($prodOrden->cant_productos * $prodOrden->precio);
        }

        $query = ProductosOrdenServicio::find()->andWhere(['orden_id' => $this->ordenServicio->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $prodOrden) { //para el update
            //Como se elimina el registro hay que rebajar lo que se habia reservado
            $prodOrden->producto->cant_reservada = round($prodOrden->producto->cant_reservada - $prodOrden->cant_productos, 3);
            $prodOrden->producto->save(false);
            $prodOrden->delete();
        }

        return true;
    }

    public function getProductosOrdenServicios()
    {
        if ($this->_productosOrdenServicios === null) {
            $this->_productosOrdenServicios = $this->ordenServicio->isNewRecord ? [] : $this->ordenServicio->productosOrdenServicios;
        }

        return $this->_productosOrdenServicios;
    }

    private function getProductosOrdenServicio($key)
    {
        $productoOrden = $key && strpos($key, 'new') === false ? ProductosOrdenServicio::findOne($key) : false;
        if (!$productoOrden) {
            $productoOrden = new ProductosOrdenServicio();
            $productoOrden->loadDefaultValues();
        }

        return $productoOrden;
    }

    public function setProductosOrdenServicios($productosOrdenServicios)
    {
        unset($productosOrdenServicios['__id__']); // remove the hidden row
        $this->_productosOrdenServicios = [];
        foreach ($productosOrdenServicios as $key => $prodOrden) {
            if (is_array($prodOrden)) {
                $this->_productosOrdenServicios[$key] = $this->getProductosOrdenServicio($key);
                $this->_productosOrdenServicios[$key]->setAttributes($prodOrden);
            } elseif ($prodOrden instanceof ProductosOrdenServicio) {
                $this->_productosOrdenServicios[$prodOrden->id] = $prodOrden;
            }
        }
    }

    /*========= Servicios - Trabajador ====================*/
    private function saveOrdenServicios()
    {
        $keep = [];
        $dupFinder = [];
        $dup;
        foreach ($this->servicioTrabajadors as $servOrden) {
            $servOrden->orden_servicio_id = $this->ordenServicio->id;

            $dup = $servOrden->servicio_id . $servOrden->trabajador_id;
            if (array_key_exists($dup, $dupFinder)) {
                $this->ordenServicio->addError("servicioTrabajadors", "Existen servicios duplicados");
                return false;
            }

            if (!$servOrden->save(false)) {
                return false;
            }
            $dupFinder[$dup] = $dup;
            $keep[] = $servOrden->id;

            //campo precio_estimado
            $this->aux_monto += $servOrden->precio;
        }

        $query = ServicioTrabajador::find()->andWhere(['orden_servicio_id' => $this->ordenServicio->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $servOrden) { //para el update
            $servOrden->delete();
        }

        return true;
    }

    public function getServicioTrabajadors()
    {
        if ($this->_servicioTrabajadors === null) {
            $this->_servicioTrabajadors = $this->ordenServicio->isNewRecord ? [] : $this->ordenServicio->servicioTrabajadors;
        }

        return $this->_servicioTrabajadors;
    }

    private function getServicioTrabajador($key)
    {
        $servicioOrden = $key && strpos($key, 'new') === false ? ServicioTrabajador::findOne($key) : false;
        if (!$servicioOrden) {
            $servicioOrden = new ServicioTrabajador();
            $servicioOrden->loadDefaultValues();
        }

        return $servicioOrden;
    }

    public function setServicioTrabajadors($servicioTrabajadors)
    {
        unset($servicioTrabajadors['__id__']); // remove the hidden row
        $this->_servicioTrabajadors = [];
        foreach ($servicioTrabajadors as $key => $servOrden) {
            if (is_array($servOrden)) {
                $this->_servicioTrabajadors[$key] = $this->getServicioTrabajador($key);
                $this->_servicioTrabajadors[$key]->setAttributes($servOrden);
            } elseif ($servOrden instanceof ServicioTrabajador) {
                $this->_servicioTrabajadors[$servOrden->id] = $servOrden;
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
            'OrdenServicio' => $this->ordenServicio,
        ];

        foreach ($this->productosOrdenServicios as $id => $prodOrden) {
            $models['ProductosOrdenServicio.' . $id] = $this->productosOrdenServicios[$id];
        }

        foreach ($this->servicioTrabajadors as $id => $servOrden) {
            $models['ServicioTrabajador.' . $id] = $this->servicioTrabajadors[$id];
        }

        return $models;
    }
}
