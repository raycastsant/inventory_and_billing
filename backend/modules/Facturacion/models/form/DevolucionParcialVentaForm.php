<?php
namespace backend\modules\Facturacion\models\form;

use backend\modules\Facturacion\models\DevolucionVenta;
use backend\modules\Facturacion\models\Devolucion;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Seguridad\controllers\TrazasVentasController;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

class DevolucionParcialVentaForm extends Model
{
    private $_devolucion;
    private $_devolucionVentas;
    private $traza_productos = "";

    private $aux_monto;

    public function rules()  {
        return [
            [['Devolucion'], 'required'],
            [['DevolucionVentas'], 'safe'],
        ];
    }

    public function afterValidate() {
        if (!Model::validateMultiple($this->getAllModels())) {
            $this->addError(null); 
        }
        parent::afterValidate();
    }

    public function saveAll() {
     /*   if (!$this->validate()) {
            return false;
        }*/

        $transaction = Yii::$app->db->beginTransaction();

        $newRecord = $this->devolucion->isNewRecord; 
        $this->aux_monto = 0;

        if (!$this->devolucion->save()) {
            $transaction->rollBack();
            return false;
        }

        $orden = $this->devolucion->getOrden();

        //save productos
        if (!$this->saveDevolucionProductos($orden)) {
            $transaction->rollBack();
            return false;
        }

        //Actualizar precio_estimado de la orden
        $orden->precio_estimado = $this->aux_monto+$orden->monto_adicional;
        if (!$orden->save()) {
            $transaction->rollBack();
            return false;
        }
        
        TrazasVentasController::insert("Devolución parcial de la orden, código: ".$orden->codigo.". 
                Productos: ".$this->traza_productos, $orden->id);

        $transaction->commit();
        return true;
    }

    public function getDevolucion() {
        return $this->_devolucion;
    }

    public function setDevolucion($devolucion) {
        if ($devolucion instanceof Devolucion) {
            $this->_devolucion = $devolucion;
        } 
        else if (is_array($devolucion)) {
            $this->_devolucion->setAttributes($devolucion);
        }
    }

    /*========= Productos ====================*/
    private function saveDevolucionProductos($orden) {
        $existenciaIni = 0;
        $prodOrden;
        foreach ($this->devolucionVentas as $dev) {
           //Verificar que se haya seleccionado 
            if($dev->seleccionado == true) {
                $dev->devolucion_id = $this->devolucion->id;
                $dev->orden_id = $this->devolucion->ordenId;

                if (!$dev->save(false)) {
                    return false;
                }
                else {
                    $existenciaIni = $dev->producto->existencia;
                    $dev->producto->existencia += $dev->cantidad;
                    $dev->producto->save(false);
                    TrazasProductosController::insert("Reajuste de inventario por devolución total de la orden (".
                                $orden->codigo."). Existencia inicial: ".$existenciaIni." .Existencia final: ". 
                                $dev->producto->existencia, $dev->producto->id);

                    if(strlen($this->traza_productos) > 0)
                        $this->traza_productos .= ", ".$dev->producto->codigo;
                    else
                        $this->traza_productos = $dev->producto->codigo;

                    $prodOrden = $orden->getProductoOrden($dev->producto->id);
                    if(isset($prodOrden)) {
                        if($prodOrden->cantidad == $dev->cantidad)  //Si se seleccionó toda la cantidad lo elimino
                            $prodOrden->delete();
                        else {
                            $prodOrden->cantidad -= $dev->cantidad;
                            if(!$prodOrden->save(false))
                                return false;

                            //actualizar campo precio_estimado de la orden
                            $this->aux_monto += ($prodOrden->cantidad*$prodOrden->precio);
                        }
                    }
                    else
                        return false;
                }
            }
            else  //actualizar campo precio_estimado de la orden
            {
                $prodOrden = $orden->getProductoOrden($dev->producto->id);
                if(isset($prodOrden)) {
                    $this->aux_monto += ($prodOrden->cantidad*$prodOrden->precio);
                }
                else
                    return false;
            }
        }

        return true;
    }

    public function getDevolucionVentas() {
        if ($this->_devolucionVentas === null) {
            $this->_devolucionVentas = $this->devolucion->isNewRecord ? [] : $this->devolucion->devolucionVentas;
        }

        return $this->_devolucionVentas;
    }

    private function getDevolucionVenta($key) {
        $devVenta = $key && strpos($key, 'new') === false ? DevolucionVenta::findOne($key) : false;
        if (!$devVenta) {
            $devVenta = new DevolucionVenta();
            $devVenta->loadDefaultValues();
        }
        
        return $devVenta;
    }

    public function setDevolucionVentas($devolucionVentas)  {
        unset($devolucionVentas['__id__']); // remove the hidden row
        $this->_devolucionVentas = [];
        foreach ($devolucionVentas as $key => $devVenta) {
            if (is_array($devVenta)) {
                $this->_devolucionVentas[$key] = $this->getDevolucionVenta($key);
                $this->_devolucionVentas[$key]->setAttributes($devVenta);
            }
            elseif ($devVenta instanceof DevolucionVenta) {
                $this->_devolucionVentas[$devVenta->id] = $devVenta;
            }
        }
    }

    public function errorSummary($form) {
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

    private function getAllModels() {
        $models = [
        'Devolucion' => $this->devolucion,
        ];

        foreach ($this->devolucionVentas as $id => $devVenta) {
            $models['DevolucionVenta.' . $id] = $this->devolucionVentas[$id];
        }

        return $models;
    }
}
?>