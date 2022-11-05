<?php

namespace backend\modules\Inventario\controllers;

use Yii;
use backend\modules\Inventario\models\Tipoproducto;
use backend\modules\Inventario\models\Producto;
use backend\modules\Inventario\models\TipoproductoSearch;
use backend\modules\Seguridad\controllers\TrazasController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Exception;

/**
 * TipoproductosController implements the CRUD actions for Tipoproducto model.
 */
class TipoproductosController extends Controller
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
                        'roles' => ['rol_admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Tipoproducto models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TipoproductoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tipoproducto model.
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
     * Creates a new Tipoproducto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tipoproducto();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tipoproducto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tipoproducto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();

        try{
            $model->eliminado = true;
            $model->save();

            $modelSinCateg = Tipoproducto::find()->where(['like', 'lower(tipo)', 'sin categ'])->one();
            if( !isset($modelSinCateg) || $modelSinCateg==null ) {
                $modelSinCateg = new Tipoproducto();
                $modelSinCateg->tipo = "Sin categoría";
                $modelSinCateg->save();
            }

            $productos = Producto::find()->where(['tipoproducto_id' => $model->id])->all();
            foreach($productos as $prod){
                $prod->tipoproducto_id = $modelSinCateg->id;
                $prod->save(false);
            }
            
            TrazasController::insert("Eliminada Categoría de productos: ".$model->tipo, Tipoproducto::tableName());
            $transaction->commit();
            Yii::$app->getSession()->setFlash('success', 'Se eliminó la categoría. Los productos relacionados están ¨Sin Categoría¨');
        }
        catch(Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('danger', 'Error eliminando la categoría');
        }
       
        return $this->redirect(['index']);
    }

    /**
     * Finds the Tipoproducto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tipoproducto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tipoproducto::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
