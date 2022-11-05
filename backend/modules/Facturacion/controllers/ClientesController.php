<?php

namespace backend\modules\Facturacion\controllers;

use Yii;
use backend\modules\Facturacion\models\Cliente;
use backend\modules\Facturacion\models\form\ClienteForm;
use backend\modules\Facturacion\models\ClienteSearch;
use backend\modules\Nomencladores\models\TipoCliente;
use backend\modules\Seguridad\controllers\TrazasController;
use backend\modules\Facturacion\controllers\VehiculosController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * ClientesController implements the CRUD actions for Cliente model.
 */
class ClientesController extends Controller
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
                        'actions' => ['create', 'update', 'delete', 'index', 'view'],
                        'roles' => ['rol_supervisor', 'rol_gestor_area'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Cliente models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClienteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cliente model.
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
     * Creates a new Cliente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
      /*  $model = new Cliente();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }*/

        $model = new ClienteForm();
        $model->cliente = new Cliente;
        $model->cliente->loadDefaultValues();
        $model->setAttributes(Yii::$app->request->post());
            
        if (Yii::$app->request->post() && $model->saveAll()) {
            TrazasController::insert("Creado cliente: ".$model->cliente->codigo."  nombre: ".$model->cliente->nombre, Cliente::tableName());
            Yii::$app->getSession()->setFlash('success', 'El cliente fue insertado satisfactoriamente');
          
            return $this->redirect(['view', 'id' => $model->cliente->id]);
        }

        $tipoclientes = ArrayHelper::map(TipoCliente::find()->where(['eliminado'=> false])->asArray()->all(), 'id', 'nombre');
        return $this->render('create', ['model' => $model, 'tipoclientes' => $tipoclientes, 'annos' => VehiculosController::getYears()] );
    }

    /**
     * Updates an existing Cliente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        //$model = $this->findModel($id);

        $model = new ClienteForm();
        $model->cliente = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->saveAll()) {
            TrazasController::insert("Actualizado cliente: ".$model->cliente->codigo."  nombre: ".$model->cliente->nombre, Cliente::tableName());
            Yii::$app->getSession()->setFlash('success', 'El cliente se actualizó satisfactoriamente');
            return $this->redirect(['view', 'id' => $model->cliente->id]);
        }
        
        $tipoclientes = ArrayHelper::map(TipoCliente::find()->where(['eliminado'=> false])->asArray()->all(), 'id', 'nombre');
        return $this->render('update', ['model' => $model, 'tipoclientes' => $tipoclientes, 'annos' => VehiculosController::getYears()] );
    }

    /**
     * Deletes an existing Cliente model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        //$this->findModel($id)->delete();
        $model = $this->findModel($id);
        $model->eliminado=true;
        $model->save();
        TrazasController::insert("Eliminado cliente: ".$model->codigo."  nombre: ".$model->nombre, Cliente::tableName());

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cliente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cliente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /*if (($model = Cliente::findOne($id)) !== null) {
            return $model;
        }*/
        if ( ($model = Cliente::findOne(['id'=>$id, 'eliminado'=>false]) ) !== null) {
            return $model;
       }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
