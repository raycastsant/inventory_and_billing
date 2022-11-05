<?php

namespace backend\modules\Facturacion\controllers;

use Yii;
use backend\modules\Facturacion\models\Vehiculo;
use backend\modules\Facturacion\models\VehiculoSearch;
use backend\modules\Facturacion\models\Cliente;
use backend\modules\Seguridad\controllers\TrazasController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * VehiculosController implements the CRUD actions for Vehiculo model.
 */
class VehiculosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'index', 'view', 'ajaxlist'],
                        'roles' => ['rol_supervisor', 'rol_gestor_area'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Vehiculo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VehiculoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vehiculo model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vehiculo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Vehiculo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado'=> false])->asArray()->all(), 'id', 'nombre');

        return $this->render('create', [
            'model' => $model, 'clientes' => $clientes, 'annos' => $this->getYears()
        ]);
    }

    public static function getYears() {
        $annos = Array();
        $maxYear = 2020;
        for($a=1935; $a <= $maxYear; $a++)
            $annos[$a] = $a;

        return $annos;
    }

    /**
     * Updates an existing Vehiculo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado'=> false])->asArray()->all(), 'id', 'nombre');

        return $this->render('update', [
            'model' => $model, 'clientes' => $clientes, 'annos' => $this->getYears(),
        ]);
    }

    /**
     * Deletes an existing Vehiculo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->eliminado=true;
        $model->save();
        TrazasController::insert("Eliminado vehículo: ".$model->chapa."  modelo: ".$model->modelo.
                                    "  marca: ".$model->marca.",  cliente: ".$model->cliente->nombre, Vehiculo::tableName());

        return $this->redirect(['index']);
    }

    /**Devuelve una lista de los vehículos 
     * de un cliente para una llamada AJAX */
    public function actionAjaxlist($id){				
        $list= Vehiculo::find()
                ->where(['cliente_id' => $id])
                ->andFilterWhere(['eliminado'=> false])
				->orderBy('chapa')
				->all();
                
        $result = "<option value=''>-Seleccionar Vehículo-</option>";
		if (!empty($list)) {
         /*   $chapa;
            $modelo;
            $id=0;*/
            $result = "";

			foreach($list as $v) {
               // $id++;
                //$chapa = "-";
               // if(isset($v->chapa))
                  //  $chapa = $v->chapa;

                //$modelo = "-";
              //  if(isset($v->modelo))
                //    $modelo = $v->modelo;

                //echo "<optgroup label='".$v->modelo."'><option value='".$v->id."'>".$v->chapa."</option></optgroup>";
                $result .= "<option value='".$v->id."'>".$v->chapa." (".$v->modelo.")</option>";
            }
            return json_encode($result);
        } 
        else {
			return json_encode($result); //echo "<option value=''>-Seleccionar Vehículo-</option>";
		}
    }

    /**Devuelve la lista de vehiculos de un cliente 
     * con el formato: "CHAPA (MODELO)" */
    public static function getVehiculosCliente($idCliente) {
        $list = Vehiculo::find()
        ->where(['cliente_id' => $idCliente])
        ->andFilterWhere(['eliminado'=> false])
        ->orderBy('chapa')
        ->all();

        $result = Array();
        if (!empty($list)) {
			foreach($list as $v) {
                $result[$v->id] = $v->chapa." (".$v->modelo.")";
			}
		} 

        return $result;
    }

    /**
     * Finds the Vehiculo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vehiculo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Vehiculo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
