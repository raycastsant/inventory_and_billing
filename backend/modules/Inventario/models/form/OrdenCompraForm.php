<?php
namespace backend\modules\Inventario\models\form;

use backend\modules\Inventario\models\OrdenCompra;
use backend\modules\Inventario\models\OrdenCompraProducto;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Facturacion\models\OrdenSeries;
use backend\modules\Economia\controllers\GastosController;
use backend\modules\Economia\controllers\TipoGastosController;
use Yii;
use yii\base\Model;
use yii\widgets\ActiveForm;

class OrdenCompraForm extends Model
{
    private $_ordenCompra;
    private $_ordenCompraProducto;

    public function rules()  {
        return [
            [['OrdenCompra'], 'required'],
            [['OrdenCompraProducto'], 'safe'],
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
        if (!$this->ordenCompra->save()) {
            $transaction->rollBack();
            return false;
        }

        //save OrdenCompra-Productos
        if (!$this->saveOrdenCompraProducto($this->ordenCompra->id)) {
            $transaction->rollBack();
            return false;
        }

        if ( ($osmodel = OrdenSeries::findOne(['tipo'=>'COMPRAS']) ) !== null) {
            $osmodel->valor = $osmodel->valor+1;
            if(!$osmodel->save()) {
                $transaction->rollBack();
                return false;
            }
        }

        $transaction->commit();
        return true;
    }

    public function getOrdenCompra() {
        return $this->_ordenCompra;
    }

    public function setOrdenCompra($ordenCompra) {
        if ($ordenCompra instanceof OrdenCompra) {
            $this->_ordenCompra = $ordenCompra;
        } 
        else if (is_array($ordenCompra)) {
            $this->_ordenCompra->setAttributes($ordenCompra);
        }
    }

    /*========= OrdenCompra-Productos ====================*/
    public function saveOrdenCompraProducto($ordenID) {
        $keep = [];
        $dupFinder = [];
        $dup;

        if($ordenID <= 0 ) {
            $this->ordenCompra->addError("OrdenCompra", "No se encontro el ID de la orden"); 
                return false;
        }
   
        $oldExis;
        $tipoGasto = TipoGastosController::getTipoGasto(1);
        foreach ($this->ordenCompraProducto as $OCP) {
            $OCP->orden_compra_id = $ordenID;
           
            $dup = $OCP->producto->id;
            if(array_key_exists($dup, $dupFinder)) {
                $this->ordenCompra->addError("Productos", "Productos duplicados"); 
                return false;
            }

            if (!$OCP->save(false)) {
                return false;
            }

            //Aumento existencia del producto
            $oldExis = $OCP->producto->existencia;
            $OCP->producto->existencia += $OCP->cantidad;
            if (!$OCP->producto->save(false)) {
                return false;
            }

            //Insertar Gasto de Productos
            GastosController::insertGasto($tipoGasto,  ($OCP->cantidad * $OCP->costo), date('Y-m-d'), 
                                    "Orden de compra productos #".$this->ordenCompra->codigo.", producto: ".$OCP->producto->codigo);

            if(!TrazasProductosController::insert("Orden de Compra: ".$this->ordenCompra->codigo.". Producto (".$OCP->producto->codigo."), Incremento de existencia de ".$oldExis." a ".$OCP->producto->existencia, $OCP->producto->id))
                return false;

            $dupFinder[$dup] = $dup;
            $keep[] = $OCP->id;
        }

        $query = OrdenCompraProducto::find()->andWhere(['orden_compra_id' => $this->ordenCompra->id]);
        if ($keep) {
            $query->andWhere(['not in', 'id', $keep]);
        }

        foreach ($query->all() as $ocp) { //para el update
            $ocp->delete();
        }

        return true;
    }

    public function getOrdenCompraProducto() {
        if ($this->_ordenCompraProducto === null) {
            $this->_ordenCompraProducto = $this->ordenCompra->isNewRecord ? [] : $this->ordenCompra->ordenCompraProductos;
        }

        return $this->_ordenCompraProducto;
    }

    private function getOCP($key) {
        $ocp = $key && strpos($key, 'new') === false ? OrdenCompraProducto::findOne($key) : false;
        if (!$ocp) {
            $ocp = new OrdenCompraProducto();
            $ocp->loadDefaultValues();
        }
        
        return $ocp;
    }

    public function setOrdenCompraProducto($ordenCompraProducto)  {
        unset($ordenCompraProducto['__id__']); // remove the hidden row
        $this->_ordenCompraProducto = [];
        foreach ($ordenCompraProducto as $key => $ocp) {
            if (is_array($ocp)) {
                $this->_ordenCompraProducto[$key] = $this->getOCP($key);
                $this->_ordenCompraProducto[$key]->setAttributes($ocp);
            }
            elseif ($ocp instanceof OrdenCompraProducto) {
                $this->_ordenCompraProducto[$ocp->id] = $ocp;
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
            'OrdenCompra' => $this->ordenCompra,
        ];

        foreach ($this->ordenCompraProducto as $id => $ocp) {
            $models['OrdenCompraProducto.' . $id] = $this->ordenCompraProducto[$id];
        }

        return $models;
    }
}
?>