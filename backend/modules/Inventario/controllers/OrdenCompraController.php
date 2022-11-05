<?php

namespace backend\modules\Inventario\controllers;

use Yii;
use backend\modules\Inventario\models\OrdenCompra;
use backend\modules\Inventario\models\form\OrdenCompraForm;
use backend\modules\Inventario\models\OrdenCompraSearch;
use backend\modules\Facturacion\models\OrdenSeries;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * OrdenCompraController implements the CRUD actions for OrdenCompra model.
 */
class OrdenCompraController extends Controller
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
                        'actions' => ['create', 'delete', 'index', 'view' ],
                        'roles' => ['rol_inventario'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all OrdenCompra models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new OrdenCompraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrdenCompra model.
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
     * Creates a new OrdenCompra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new OrdenCompraForm();
        $model->ordenCompra = new OrdenCompra;
        $model->ordenCompra->loadDefaultValues();
        $model->setAttributes(Yii::$app->request->post());
            
        if (Yii::$app->request->post()) 
        {
            $model->ordenCompra->moneda_id = 2;  //CUP

            if($model->saveAll()) {
                Yii::$app->getSession()->setFlash('success', 'La orden de compra fue insertada satisfactoriamente');
                return $this->redirect(['view', 'id' => $model->ordenCompra->id]);
            }
            else {
                Yii::$app->getSession()->setFlash('danger', 'Error creando la orden');
            }
        }

        $serie = $this->getOrdenSerie();
        return $this->render('create', ['model' => $model, 'serie' => $serie] );
    }

    /**
     * Updates an existing OrdenCompra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   /* public function actionUpdate($id) {
        $model = new OrdenCompraForm();
        $model->ordenCompra = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->saveAll()) {
            Yii::$app->getSession()->setFlash('success', 'La orden de compra se actualizó satisfactoriamente');
            
            return $this->redirect(['view', 'id' => $model->ordenCompra->id]);
        }
        
        return $this->render('update', ['model' => $model] );
    }*/

    /**
     * Deletes an existing OrdenCompra model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the OrdenCompra model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrdenCompra the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = OrdenCompra::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function getOrdenSerie() {
        return OrdenSeries::find()->having(['tipo'=> 'COMPRAS'])->max('valor');  
    }
}
