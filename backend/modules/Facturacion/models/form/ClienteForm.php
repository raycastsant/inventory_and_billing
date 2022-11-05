<?php
namespace backend\modules\Facturacion\models\form;

use backend\modules\Facturacion\models\Cliente;
use backend\modules\Facturacion\models\Vehiculo;
use backend\modules\Facturacion\models\ClienteEmpresaResponsable;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;


class ClienteForm extends Model
{
    private $_cliente;
    private $_vehiculos;
    private $_empresaResponsables;

    public function rules()  {
        return [
            [['Cliente'], 'required'],
            [['Vehiculos', 'EmpresaResponsables'], 'safe'],
        ];
    }

    public function afterValidate() {
        if (!Model::validateMultiple($this->getAllModels())) {
            $this->addError(null); // add an empty error to prevent saving
        }
        parent::afterValidate();
    }

    public function saveAll() {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        if (!$this->cliente->save()) {
            $transaction->rollBack();
            return false;
        }

        //save vehiculos
        if (!$this->saveVehiculos($this->cliente->id)) {
            $transaction->rollBack();
            return false;
        }

        //save Responsables de la empresa
        if($this->cliente->tipoCliente->nombre != 'Empresa') {
            if (!$this->deleteEmpresaResponsables($this->cliente->id)) {
                $transaction->rollBack();
                return false;
            }
        }
        else {
            if (!$this->saveEmpresaResponsables($this->cliente->id)) {
                $transaction->rollBack();
                return false;
            }
        }

        $transaction->commit();
        return true;
    }

    public function getCliente() {
        return $this->_cliente;
    }

    public function setCliente($cliente) {
        if ($cliente instanceof Cliente) {
            $this->_cliente = $cliente;
        } 
        else if (is_array($cliente)) {
            $this->_cliente->setAttributes($cliente);
        }
    }

    /*========= Vehiculos ====================*/
    public function saveVehiculos($clienteID) {
        $keep = [];
        $dupFinder = [];
        $dup;

        if($clienteID <= 0 ) {
            $this->cliente->addError("Vehículos", "No se encontro el ID del cliente"); 
            return false;
        }

        if(count($this->vehiculos) <= 0 ) {
            $this->cliente->addError("Cliente", "Debe insertar al menos un vehículo"); 
            return false;
        }
        
        foreach ($this->vehiculos as $v) {
            $v->cliente_id = $clienteID;
           
            $dup = $v->chapa;
            if(array_key_exists($dup, $dupFinder)) {
                $this->cliente->addError("Vehículos", "Vehículos duplicados"); 
                return false;
            }

            /*$year = $v->anno+"";
            if(strlen($year) == 1)
                $year = "0"+$year;
            else
            if(strlen($year) == 3)
                $year = "1"+$year;
            else {
                if( preg_match('#^19#')===0 )
            }*/
            
            if (!$v->save(false)) {
                return false;
            }
            $dupFinder[$dup] = $dup;
            $keep[] = $v->id;
        }

        $query = Vehiculo::find()->andWhere(['cliente_id' => $this->cliente->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $v) { //para el update
            $v->delete();
        }

        return true;
    }

    public function getVehiculos() {
        if ($this->_vehiculos === null) {
            $this->_vehiculos = $this->cliente->isNewRecord ? [] : $this->cliente->vehiculos;
        }

        return $this->_vehiculos;
    }

    private function getVehiculo($key) {
        $v = $key && strpos($key, 'new') === false ? Vehiculo::findOne($key) : false;
        if (!$v) {
            $v = new Vehiculo();
            $v->loadDefaultValues();
        }
        
        return $v;
    }

    public function setVehiculos($vehiculos)  {
        unset($vehiculos['__id__']); // remove the hidden row
        $this->_vehiculos = [];
        foreach ($vehiculos as $key => $v) {
            if (is_array($v)) {
                $this->_vehiculos[$key] = $this->getVehiculo($key);
                $this->_vehiculos[$key]->setAttributes($v);
            }
            elseif ($v instanceof Vehiculo) {
                $this->_vehiculos[$v->id] = $v;
            }
        }
    }

     /*========= Empresa responsables ====================*/
     public function saveEmpresaResponsables($clienteID) {
        $keep = [];
        $dupFinder = [];
        $dup;

        if($clienteID <= 0 ) {
            $this->cliente->addError("Empresa Responsables", "No se encontro el ID del cliente"); 
            return false;
        }

        foreach ($this->empresaResponsables as $ER) {
            $ER->cliente_id = $clienteID;
           
            $dup = $ER->nombre;
            if(array_key_exists($dup, $dupFinder)) {
                $this->cliente->addError("Empresa Responsables", "Nombres duplicados"); 
                return false;
            }

            if (!$ER->save(false)) {
                return false;
            }
            $dupFinder[$dup] = $dup;
            $keep[] = $ER->id;
        }

        /*if(count($keep) <= 0) {
            $this->cliente->addError("Cliente", "Debe insertar al menos un responsable de empresa"); 
            return false;
        }*/

        $query = ClienteEmpresaResponsable::find()->andWhere(['cliente_id' => $clienteID]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $ER) { //para el update
            $ER->delete();
        }

        return true;
    }

    private function deleteEmpresaResponsables() {
        $query = ClienteEmpresaResponsable::find()->andWhere(['cliente_id' => $this->cliente->id]);
        foreach ($query->all() as $ER) { 
            $ER->delete();
        }

        return true;
    }

    public function getEmpresaResponsables() {
        if ($this->_empresaResponsables === null) {
            $this->_empresaResponsables = $this->cliente->isNewRecord ? [] : $this->cliente->clienteEmpresaResponsables;
        }

        return $this->_empresaResponsables;
    }

    private function getEmpresaResponsable($key) {
        $v = $key && strpos($key, 'new') === false ? ClienteEmpresaResponsable::findOne($key) : false;
        if (!$v) {
            $v = new ClienteEmpresaResponsable();
            $v->loadDefaultValues();
        }
        
        return $v;
    }

    public function setEmpresaResponsables($responsables)  {
        unset($responsables['__id__']); // remove the hidden row
        $this->_empresaResponsables = [];
        foreach ($responsables as $key => $v) {
            if (is_array($v)) {
                $this->_empresaResponsables[$key] = $this->getEmpresaResponsable($key);
                $this->_empresaResponsables[$key]->setAttributes($v);
            }
            elseif ($v instanceof ClienteEmpresaResponsable) {
                $this->_empresaResponsables[$v->id] = $v;
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
            'Cliente' => $this->cliente,
        ];

        foreach ($this->vehiculos as $id => $v) {
            $models['Vehiculo.' . $id] = $this->vehiculos[$id];
        }

        foreach ($this->empresaResponsables as $id => $v) {
            $models['ClienteEmpresaResponsable.' . $id] = $this->empresaResponsables[$id];
        }

        return $models;
    }
}
?>