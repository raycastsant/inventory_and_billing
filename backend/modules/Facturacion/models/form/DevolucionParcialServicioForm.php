<?php
namespace backend\modules\Facturacion\models\form;

use backend\modules\Facturacion\models\DevolucionServicio;
use backend\modules\Facturacion\models\Devolucion;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Seguridad\controllers\TrazasServiciosController;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

class DevolucionParcialServicioForm extends Model
{
    private $_devolucion;
    private $_devolucionServicios;
    private $traza_productos = "";

    private $aux_monto;

    public function rules()  {
        return [
            [['Devolucion'], 'required'],
            [['DevolucionServicios'], 'safe'],
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
        $orden->precio_estimado = $this->aux_monto + $orden->getMontoServicios();
        if (!$orden->save()) {
            $transaction->rollBack();
            return false;
        }
        
        TrazasServiciosController::insert("Devolución parcial de la orden, código: ".$orden->codigo.". 
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
        foreach ($this->devolucionServicios as $dev) {
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
                        if($prodOrden->cant_productos == $dev->cantidad)  //Si se seleccionó toda la cantidad lo elimino
                            $prodOrden->delete();
                        else {
                            $prodOrden->cant_productos -= $dev->cantidad;
                            if(!$prodOrden->save(false))
                                return false;

                            //actualizar campo precio_estimado de la orden
                            $this->aux_monto += ($prodOrden->cant_productos*$prodOrden->precio);
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
                    $this->aux_monto += ($prodOrden->cant_productos*$prodOrden->precio);
                }
                else
                    return false;
            }
        }

        return true;
    }

    public function getDevolucionServicios() {
        if ($this->_devolucionServicios === null) {
            $this->_devolucionServicios = $this->devolucion->isNewRecord ? [] : $this->devolucion->devolucionServicios;
        }

        return $this->_devolucionServicios;
    }

    private function getDevolucionServicio($key) {
        $devServicio = $key && strpos($key, 'new') === false ? DevolucionServicio::findOne($key) : false;
        if (!$devServicio) {
            $devServicio = new DevolucionServicio();
            $devServicio->loadDefaultValues();
        }
        
        return $devServicio;
    }

    public function setDevolucionServicios($devolucionServicios)  {
        unset($devolucionServicios['__id__']); // remove the hidden row
        $this->_devolucionServicios = [];
        foreach ($devolucionServicios as $key => $devServicio) {
            if (is_array($devServicio)) {
                $this->_devolucionServicios[$key] = $this->getDevolucionServicio($key);
                $this->_devolucionServicios[$key]->setAttributes($devServicio);
            }
            elseif ($devServicio instanceof DevolucionServicio) {
                $this->_devolucionServicios[$devServicio->id] = $devServicio;
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

        foreach ($this->devolucionServicios as $id => $devServicio) {
            $models['DevolucionServicio.' . $id] = $this->devolucionServicios[$id];
        }

        return $models;
    }
}
?>