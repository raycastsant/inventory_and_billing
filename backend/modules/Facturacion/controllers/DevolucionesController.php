<?php

namespace backend\modules\Facturacion\controllers;

use Yii;
use backend\modules\Facturacion\models\Devolucion;
use backend\modules\Facturacion\models\DevolucionVenta;
use backend\modules\Facturacion\models\DevolucionServicio;
use backend\modules\Facturacion\models\OrdenVenta;
use backend\modules\Facturacion\models\OrdenServicio;
use backend\modules\Facturacion\models\form\DevolucionParcialVentaForm;
use backend\modules\Facturacion\models\form\DevolucionParcialServicioForm;
use backend\modules\Facturacion\models\DevolucionVentaSearch;
use backend\modules\Facturacion\models\DevolucionServicioSearch;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Seguridad\controllers\TrazasServiciosController;
use backend\modules\Seguridad\controllers\TrazasVentasController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DevolucionesController implements the CRUD actions for Devolucion model.
 */
class DevolucionesController extends Controller
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
                    'create' => ['POST'],
                    'devolucion' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'devolucion', 'index-ventas', 'index-servicios', 'view'],
                        'roles' => ['rol_supervisor', 'rol_gestor_area'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Devolucion models.
     * @return mixed
     */
    public function actionIndexVentas() {
        $searchModel = new DevolucionVentaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexServicios() {
        $searchModel = new DevolucionServicioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Devolucion model.
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
     * Creates a new Devolucion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDevolucion($parcial, $ordenId, $is_ventas) {
        $model = new Devolucion();

        if (/*Yii::$app->request->isPost && */isset($parcial) && isset($ordenId) && isset($is_ventas)) {
            $model->parcial = $parcial;
            $model->is_venta = $is_ventas;
            $model->ordenId = $ordenId;
            $model->fecha = date('Y-m-d');

            $url = 'ordenventas/view';
            if(!$is_ventas)
                $url = 'ordenservicios/view';

            if(!$parcial) {   //Total
                $transaction = Yii::$app->db->beginTransaction();

                if($model->save()) {

                    if($is_ventas) {  //Orden de Ventas
                       $this->devolucionTotalVentas($model->id, $ordenId);
                    }
                    else {  //Orden de Servicios
                        $this->devolucionTotalServicios($model->id, $ordenId);
                    }

                    $transaction->commit();
                }

                return $this->redirect([$url, 'id'=>$ordenId]);
            } 
            else {   //Parcial
                $modelForm = new DevolucionParcialVentaForm();
                $modelForm->devolucion = $model;

                return $this->render('create', [
                    'modelForm' => $modelForm,
                ]);
            }
        }
        else {
            return $this->redirect(['index']);
        }
    }

    private function devolucionTotalVentas($devolucionId, $ordenId) {
        $orden = $this->findOrdenVentas($ordenId);
        $existenciaIni = 0;
        $devolucionV;

        foreach ($orden->getProductosList() as $prodOrden) { 
            $existenciaIni = $prodOrden->producto->existencia;

            $devolucionV = new DevolucionVenta();
            $devolucionV->devolucion_id = $devolucionId;
            $devolucionV->producto_id = $prodOrden->producto->id;
            $devolucionV->orden_id = $ordenId;
            $devolucionV->cantidad = $prodOrden->cantidad;
            $devolucionV->save(false);

            $prodOrden->producto->existencia += $prodOrden->cantidad;
            $prodOrden->producto->save(false);
            $prodOrden->delete();

            TrazasProductosController::insert("Reajuste de inventario por devolución total de la orden (".
                        $orden->codigo."). Existencia inicial: ".$existenciaIni." .Existencia final: ". 
                        $prodOrden->producto->existencia, $prodOrden->producto->id);
        }

        $orden->precio_estimado = $orden->monto_adicional;
        $orden->save(false);

        TrazasVentasController::insert("Devolución total de la orden, código: ".$orden->codigo, $orden->id);
    }

    private function devolucionTotalServicios($devolucionId, $ordenId) {
        $orden = $this->findOrdenServicios($ordenId);
        $existenciaIni = 0;
        $devolucionV;

        foreach ($orden->getProductosList() as $prodOrden) { 
            $existenciaIni = $prodOrden->producto->existencia;

            $devolucionV = new DevolucionServicio();
            $devolucionV->devolucion_id = $devolucionId;
            $devolucionV->producto_id = $prodOrden->producto->id;
            $devolucionV->orden_id = $ordenId;
            $devolucionV->cantidad = $prodOrden->cant_productos;
            $devolucionV->save(false);

            $prodOrden->producto->existencia += $prodOrden->cant_productos;
            $prodOrden->producto->save(false);
            $prodOrden->delete();

            TrazasProductosController::insert("Reajuste de inventario por devolución total de la orden (".
                        $orden->codigo."). Existencia inicial: ".$existenciaIni." .Existencia final: ". 
                        $prodOrden->producto->existencia, $prodOrden->producto->id);
        }

        $orden->precio_estimado = $orden->getMontoServicios();
        $orden->save(false);

        TrazasServiciosController::insert("Devolución total de la orden, código: ".$orden->codigo, $orden->id);
    }

    public function actionCreate() {
        if(Yii::$app->request->post()['Devolucion']['is_venta'] == true) {
            $modelForm = new DevolucionParcialVentaForm();
            $modelForm->devolucion = new Devolucion();
            $modelForm->devolucion->setAttributes(Yii::$app->request->post());
            $modelForm->setAttributes(Yii::$app->request->post());
        }
            
        else {
            $modelForm = new DevolucionParcialServicioForm();
            $modelForm->devolucion = new Devolucion();
            $modelForm->devolucion->setAttributes(Yii::$app->request->post());
            $modelForm->setAttributes(Yii::$app->request->post());
        }
            
        if ($modelForm->saveAll()) {
            return $this->redirect(['view', 'id' => $modelForm->devolucion->id]);
        }
        else {
            Yii::$app->getSession()->setFlash('danger', 'Ocurrió un error insertando a devolución');
          //  return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Devolucion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Devolucion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Devolucion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findOrdenVentas($id) {
        if (($model = OrdenVenta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findOrdenServicios($id) {
        if (($model = OrdenServicio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
