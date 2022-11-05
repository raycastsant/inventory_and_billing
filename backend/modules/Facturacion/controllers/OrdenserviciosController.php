<?php

namespace backend\modules\Facturacion\controllers;

use Yii;
use backend\modules\Facturacion\models\OrdenServicio;
use backend\modules\Facturacion\models\OrdenServicioSearch;
use backend\modules\Facturacion\models\Cliente;
use backend\modules\Facturacion\models\form\OrdenServicioForm;
use backend\modules\Facturacion\controllers\CeoInfosController;
use backend\modules\Nomencladores\models\Servicio;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Facturacion\models\Trabajador;
use backend\modules\Seguridad\controllers\TrazasServiciosController;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Facturacion\controllers\MonedasController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\Nomencladores\models\EstadoOrden;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use backend\modules\Facturacion\ESTADOS;
use backend\modules\Facturacion\models\OrdenSeries;
use kartik\mpdf\Pdf;
use yii\helpers\Html;

/**
 * OrdenserviciosController implements the CRUD actions for OrdenServicio model.
 */
class OrdenserviciosController extends Controller //_OrdenController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'create', 'update', 'delete', 'index', 'view', 'imprimir-oferta',
                            'facturar-orden', 'imprimir-factura', 'cancelar-orden', 'set-cobrada'
                        ],
                        'roles' => ['rol_gestor_area', 'rol_supervisor'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-ofertas-ajax', 'get-facturas-pendientes-ajax', 'ofertas-count', 'facturas-count'],
                        'roles' => ['rol_user'],
                    ],
                ],
            ],
            // OrdenServicioBehavior::className(),
        ];
    }

    /**
     * Lists all OrdenServicio models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrdenServicioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrdenServicio model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $moneda1 = MonedasController::findMonedaById($model->moneda_id);  //CUC
        if (!$moneda1) {
            echo '<h1>No se encontró la moneda</h1>';
            return false;
        }

        $cambio = 1;  //Establezco el cambio en 1x1 por defecto
        $cambioCosto = 1;
        $moneda_salida = $moneda1->nombre;

        //Si es una orden en CUC, ahora el cambio es a CUP
        if ($moneda1->nombre == 'CUC') {
            $cambioCosto = 0.04;   //Para cambiar los costos de CUP a CUC
        }

        $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado' => false])->orderBy(['nombre' => SORT_ASC])->asArray()->all(), 'id', 'nombre');

        return $this->render('view', [
            'model' => $model,
            'cambio' => $cambio,
            'cambioCosto' => $cambioCosto,
            'moneda_salida' => $moneda_salida,
            'clientes' => $clientes,
        ]);
    }

    /**
     * Creates a new OrdenServicio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrdenServicioForm();
        $model->ordenServicio = new OrdenServicio;
        $model->ordenServicio->loadDefaultValues();
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post()) {
            $serie = $this->getOrdenSerie();
            $showWarning = false;

            if ($serie !== $model->ordenServicio->serie) {
                $showWarning = true;
                $date = date('Y');
                $model->ordenServicio->codigo = $date . '/' . sprintf("%06d", $serie);
            }

            $model->ordenServicio->moneda_id = 2;  //CUP

            if ($model->saveAll()) {
                Yii::$app->getSession()->setFlash('success', 'La orden fue creada satisfactoriamente');
                if ($showWarning)
                    Yii::$app->getSession()->setFlash('info', 'El código de la orden se reestableció a : ' . $model->ordenServicio->codigo);

                return $this->redirect(['update', 'id' => $model->ordenServicio->id]);
            }
        }


        $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado' => false])->asArray()->all(), 'id', 'nombre');
        $servicios = ArrayHelper::map(Servicio::find()->having(['eliminado' => false])->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
        $trabajadores = ArrayHelper::map(Trabajador::find()->having(['eliminado' => false])->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
        $areas = ArrayHelper::map(Area::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'nombre');
        $serie = $this->getOrdenSerie();

        if (($m_estado = EstadoOrden::findOne(['estado' => ESTADOS::ABIERTO])) !== null) {
            return $this->render('create', [
                'model' => $model, 'clientes' => $clientes,
                'estado_id' => $m_estado->id,
                'area_id' => Yii::$app->session->get('area'), 'servicios' => $servicios,
                'trabajadores' => $trabajadores, 'serie' => $serie, 'areas' => $areas
            ]);
        }
    }

    /**
     * Updates an existing OrdenServicio model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new OrdenServicioForm();
        $model->ordenServicio = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->saveAll()) {
            Yii::$app->getSession()->setFlash('success', 'La orden fue actualizada satisfactoriamente');
            return $this->redirect(['view', 'id' => $model->ordenServicio->id]);
        }

        if ($model->ordenServicio->estadoOrden->estado == ESTADOS::ABIERTO) {
            $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado' => false])->asArray()->all(), 'id', 'nombre');

            $servicios = ArrayHelper::map(Servicio::find()->having(['eliminado' => false])->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
            $trabajadores = ArrayHelper::map(Trabajador::find()->having(['eliminado' => false])->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
            $areas = ArrayHelper::map(Area::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'nombre');

            return $this->render('update', [
                'model' => $model, 'clientes' => $clientes, 'servicios' => $servicios,
                'trabajadores' => $trabajadores, 'areas' => $areas
            ]);
        } else {
            return $this->render('view', ['model' => $model->ordenServicio]);
        }
    }

    /** Facturar una orden. El estado resultante depende de la variable COBRADO */
    public function actionFacturarOrden($ordenId, $cobrado = false)
    {
        if (Yii::$app->request->isPost) {
            $ordenServicio = $this->findModel($ordenId);

            if ($ordenServicio != null) {
                $state = ESTADOS::FACTURADO;
                if ($cobrado == true) {
                    $state = ESTADOS::COBRADO;
                }

                if (($m_estado = EstadoOrden::findOne(['estado' => $state])) !== null) {
                    $ordenServicio->estado_orden_id = $m_estado->id;

                    if ($m_estado->estado == ESTADOS::COBRADO) {
                        $ordenServicio->fecha_cobrada = date('Y-m-d');

                        if ($ordenServicio->fecha_facturada == null)
                            $ordenServicio->fecha_facturada = date('Y-m-d');   //Facturada y cobrada
                    } else {   //Facturado
                        $ordenServicio->fecha_facturada = date('Y-m-d');
                    }

                    $transaction = Yii::$app->db->beginTransaction();

                    //Validar que exista el producto en almacen antes de facturar
                    $prodsList = $ordenServicio->productosOrdenServicios;
                    $sinExistencias = [];
                    foreach ($prodsList as $prodOrden) {
                        if ($prodOrden->cant_productos > $prodOrden->producto->existencia) {
                            $sinExistencias[] = $prodOrden->producto->nombre;
                        }
                    }

                    if (isset($sinExistencias) && count($sinExistencias) > 0) {
                        $msg = 'Imposible Facturar. Los siguientes productos no pesentan suficiencia en almacén :  <br>';
                        foreach ($sinExistencias as $prod) {
                            $msg = $msg . $prod . ', ';
                        }
                        Yii::$app->getSession()->setFlash('warning', $msg);
                        $transaction->rollBack();
                        return $this->redirect(['view', 'id' => $ordenId]);
                    } else {
                        if ($ordenServicio->save()) {
                            //Rebajar de inventario y de la reserva
                            foreach ($prodsList as $prodOrden) {
                                $existenciaIni = $prodOrden->producto->existencia;
                                $prodOrden->producto->existencia = ($prodOrden->producto->existencia - $prodOrden->cant_productos);
                                $prodOrden->producto->cant_reservada = ($prodOrden->producto->cant_reservada - $prodOrden->cant_productos);

                                if (!$prodOrden->producto->save()) {
                                    $errors = "  Error guardando producto <b>" . $prodOrden->producto->codigo . "</b>";
                                    foreach ($prodOrden->producto->getErrors() as $e) {
                                        foreach ($e as $se) {
                                            $errors .= '<br>   -' . substr($se, strpos($se, '\"'), strlen($se));
                                            //  file_put_contents('E:\\e.txt', serialize($se));
                                        }
                                    }

                                    Yii::$app->getSession()->setFlash('danger', 'Error rebajando del inventario. No se realizó la operación' . '<br>' . $errors);
                                    $transaction->rollBack();
                                    return $this->redirect(['view', 'id' => $ordenId]);
                                }

                                TrazasProductosController::insert("Reajuste de inventario por facturación de la orden (" . $ordenServicio->codigo . "). Existencia inicial: " . $existenciaIni . " .Existencia final: " . $prodOrden->producto->existencia, $prodOrden->producto->id);
                            }

                            $traza = "Facturar orden: ";
                            if ($cobrado == true)
                                $traza = "Facturar y cobrar orden: ";
                            TrazasServiciosController::insert($traza . $ordenServicio->codigo, $ordenServicio->id);

                            $transaction->commit();
                            Yii::$app->getSession()->setFlash('success', 'La orden se facturó correctamente');

                            return $this->redirect(['view', 'id' => $ordenId]);
                        } else {
                            $transaction->rollBack();
                        }
                    }
                }
            }
        } else {
            return $this->redirect(['index']);
        }

        return false;
    }

    public function actionSetCobrada($ordenId)
    {
        if (Yii::$app->request->isPost) {
            $orden = $this->findModel($ordenId);
            if (($m_estado = EstadoOrden::findOne(['estado' => ESTADOS::COBRADO])) !== null) {
                $orden->estado_orden_id = $m_estado->id;
                if ($orden->save()) {
                    TrazasServiciosController::insert("Cobrar orden: " . $orden->codigo, $orden->id);

                    Yii::$app->getSession()->setFlash('success', 'El estado de la orden ahora es COBRADO');
                    return $this->redirect(['view', 'id' => $ordenId]);
                }
            }
        } else
            return $this->redirect(['index']);

        return false;
    }

    public function actionCancelarOrden($ordenId)
    {
        if (Yii::$app->request->isPost) {
            $orden = $this->findModel($ordenId);
            $transaction = Yii::$app->db->beginTransaction();

            //Si el estado es abierto solo se quita de la reserva los productos
            if ($orden->estadoOrden->estado == ESTADOS::ABIERTO) {
                $prodsList = $orden->productosOrdenServicios;
                foreach ($prodsList as $prodOrden) {
                    $prodOrden->producto->cant_reservada = ($prodOrden->producto->cant_reservada - $prodOrden->cant_productos);

                    if (!$prodOrden->producto->save()) {
                        Yii::$app->getSession()->setFlash('danger', 'Error del sistema. No se realizó la operación');
                        $transaction->rollBack();
                        return $this->redirect(['view', 'id' => $ordenId]);
                    }
                }
            } else
            if ($orden->estadoOrden->estado != ESTADOS::CANCELADO) {  //No se supone q se cancele una orden cancelada pero se verifica para mayor seguridad
                $prodsList = $orden->productosOrdenServicios;
                foreach ($prodsList as $prodOrden) {
                    $existenciaIni = $prodOrden->producto->existencia;
                    $prodOrden->producto->existencia = ($prodOrden->producto->existencia + $prodOrden->cant_productos);

                    if (!$prodOrden->producto->save()) {
                        Yii::$app->getSession()->setFlash('danger', 'Error del sistema. No se realizó la operación');
                        $transaction->rollBack();
                        return $this->redirect(['view', 'id' => $ordenId]);
                    }

                    TrazasProductosController::insert("Reajuste de inventario por cancelación de la orden (" . $orden->codigo . "). Existencia inicial: " . $existenciaIni . " .Existencia final: " . $prodOrden->producto->existencia, $prodOrden->producto->id);
                }
            }

            if (($m_estado = EstadoOrden::findOne(['estado' => ESTADOS::CANCELADO])) !== null) {
                $orden->estado_orden_id = $m_estado->id;
                if ($orden->save()) {
                    TrazasServiciosController::insert("Cancelar orden: " . $orden->codigo, $orden->id);

                    $transaction->commit();
                    Yii::$app->getSession()->setFlash('success', 'La orden fue cancelada');
                } else {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('danger', 'Error al realizar la operación');
                }

                return $this->redirect(['view', 'id' => $ordenId]);
            }
        } else
            return $this->redirect(['index']);

        return false;
    }

    /** Imprimir Oferta de la Orden a PDF */
    public function actionImprimirOferta($ordenid, $moneda2id, $printfirma = '0')
    {
        if (isset($ordenid)) {
            $model = $this->findModel($ordenid);

            if (count($model->getServicios()) <= 0) {
                echo '<h1>La orden no tiene datos</h1>';
                return false;
            }

            $moneda1 = MonedasController::findMonedaById($model->moneda_id);
            if (!$moneda1 || !$moneda2id) {
                echo '<h1>No se encontró la moneda</h1>';
                return false;
            }

            $cambio = 1;  //Establezco el cambio en 1x1 por defecto
            $moneda_salida = $moneda1->nombre;
            if ($moneda1->id != $moneda2id) {   //Verificar que no sea la misma moneda
                $mc = $moneda1->findCambioMoneda($moneda2id);
                if ($mc != null) {
                    $cambio = $mc->valor;
                    $moneda_salida = $mc->m2->nombre;
                }
            }

            $ceoInfo = CeoInfosController::getCeoInfo(); // $this->findCeoInfo();
            $content = $this->renderPartial('_print-oferta-pdf', [
                'model' => $model, 'cambio' => $cambio,
                'moneda_salida' => $moneda_salida, 'ceoInfo' => $ceoInfo
            ]);

            $methods['SetFooter'] = $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo]);

            //Verificar que el usuario sea jefe de area para imprimir
            $user_id = Yii::$app->user->getId();
            $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
            $firma =  ($printfirma == '1' /*&& in_array(UserRole::ROL_JEFE_AREA, $keys)*/);
            if ($firma == true)
                $methods['SetWatermarkImage'] = ['/InvFactServices/backend/web/images/Fa1.png', 3, array(30, 20), array(130, 240)];

            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_LETTER,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Krajee Report Title', 'showWatermarkImage' => $firma],
                'methods' => $methods,
                /*[
                   // 'SetHeader'=>['Oferta de Servicios'],
                    'SetFooter'=> $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo, 'printfirma' => $printfirma]),   //['{PAGENO}'],
                ]*/
            ]);

            $pdf->filename = "Oferta " . $model->codigo . ".pdf";
            return $pdf->render();
        } else {
            Yii::$app->getSession()->setFlash('danger', 'Error obteniendo los datos de la Orden');
            return false;
        }
    }

    public function actionImprimirFactura($ordenid, $moneda2id, $printfirma = '0')
    {
        if (isset($ordenid)) {
            $model = $this->findModel($ordenid);

            if (count($model->getServicios())  <= 0) {
                echo '<h1>La orden no tiene datos</h1>';
                return false;
            }

            $moneda1 = MonedasController::findMonedaById($model->moneda_id);
            if (!$moneda1 || !$moneda2id) {
                echo '<h1>No se encontró la moneda</h1>';
                return false;
            }

            $cambio = 1;  //Establezco el cambio en 1x1 por defecto
            $moneda_salida = $moneda1->nombre;
            if ($moneda1->id != $moneda2id) {   //Verificar que no sea la misma moneda
                $mc = $moneda1->findCambioMoneda($moneda2id);
                if ($mc != null) {
                    $cambio = $mc->valor;
                    $moneda_salida = $mc->m2->nombre;
                }
            }

            $ceoInfo = CeoInfosController::getCeoInfo(); // $this->findCeoInfo();
            $content = $this->renderPartial('_print-factura-pdf', [
                'model' => $model, 'cambio' => $cambio,
                'moneda_salida' => $moneda_salida, 'ceoInfo' => $ceoInfo
            ]);

            $methods['SetFooter'] = $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo]);

            //Verificar que el usuario sea jefe de area para imprimir
            $user_id = Yii::$app->user->getId();
            $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
            $firma =  ($printfirma == '1' /*&& in_array(UserRole::ROL_JEFE_AREA, $keys)*/);
            if ($firma == true)
                $methods['SetWatermarkImage'] = ['/InvFactServices/backend/web/images/Fa1.png', 3, array(30, 20), array(130, 240)];

            $pdf = new Pdf([
                'mode' => Pdf::MODE_CORE,
                'format' => Pdf::FORMAT_LETTER,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => '.kv-heading-1{font-size:18px}',
                'options' => ['title' => 'Krajee Report Title', 'showWatermarkImage' => $firma],
                'methods' => $methods,
                /*[
                    //'SetHeader'=>['Factura de servicios'],
                    'SetFooter'=> $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo]),   //['{PAGENO}'],
                ]*/
            ]);

            $pdf->filename = "Factura " . $model->codigo . ".pdf";
            return $pdf->render();
        } else {
            Yii::$app->getSession()->setFlash('danger', 'Error obteniendo los datos de la Orden');
            return false;
        }
    }

    private function getOfertasAjaxQuery()
    {
        $query = OrdenServicio::find();
        $query->select('orden_servicios.id, orden_servicios.codigo, orden_servicios.fecha_iniciada, clientes.nombre as cliente');
        $query->innerJoin('estado_orden', 'orden_servicios.estado_orden_id=estado_orden.id')
            ->innerJoin('clientes', 'orden_servicios.cliente_id=clientes.id');
        $query->andFilterWhere(['like', 'estado_orden.estado', ESTADOS::ABIERTO]);
        $query->andFilterWhere(['=', 'orden_servicios.eliminado', false]);
        $query->orderBy(['fecha_iniciada' => SORT_DESC]);

        $area_id = -1;
        if (Yii::$app->session->has('area')) {
            $area_id = Yii::$app->session->get('area');
            if ($area_id > 0)
                $query->andFilterWhere(['=', 'orden_servicios.area_id', $area_id]);
        }

        return $query;
    }

    /**Devuelve las ordenes con estado de OFERTA */
    public function actionGetOfertasAjax()
    {
        if (!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        $query = $this->getOfertasAjaxQuery();

        $totalRows = $query->count();
        $filtered = $totalRows;

        //Paginador
        if (isset($_POST["length"]) && $_POST["length"] != -1) {
            $query->limit($_POST["length"])->offset($_POST["start"]);
        }

        $result = $query->asArray()->all();

        $data = array();
        $i = 0;
        foreach ($result as $orden) {
            $sub_array = array();
            $sub_array[] = $orden['codigo'];
            $sub_array[] = $orden['fecha_iniciada'];
            $sub_array[] = $orden['cliente'];
            $sub_array[] = Html::a('<span class="glyphicon glyphicon-search"></span>', ['view', 'id' => $orden['id']], ['class' => 'btn btn-default btn-xs']);
            $data[] = $sub_array;
            $i++;
        }

        $draw = 0;
        if (isset($_POST["draw"]))
            $draw = intval($_POST["draw"]);
        $output = array(
            "draw"       =>  $draw,
            "recordsTotal"   =>  $totalRows,
            "recordsFiltered"  =>  $filtered,
            "data"       =>  $data
        );

        return json_encode($output);
    }

    public function actionOfertasCount()
    {
        if (!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        $query = $this->getOfertasAjaxQuery();

        return json_encode($query->count());
    }

    private function getFacturasPendQuery()
    {
        $query = OrdenServicio::find();
        $query->select('orden_servicios.id, orden_servicios.codigo, orden_servicios.fecha_iniciada, clientes.nombre as cliente');
        $query->innerJoin('estado_orden', 'orden_servicios.estado_orden_id=estado_orden.id')
            ->innerJoin('clientes', 'orden_servicios.cliente_id=clientes.id');
        $query->andFilterWhere(['like', 'estado_orden.estado', ESTADOS::FACTURADO]);
        $query->andFilterWhere(['=', 'orden_servicios.eliminado', false]);
        $query->orderBy(['fecha_iniciada' => SORT_DESC]);

        $area_id = -1;
        if (Yii::$app->session->has('area')) {
            $area_id = Yii::$app->session->get('area');
            if ($area_id > 0)
                $query->andFilterWhere(['=', 'orden_servicios.area_id', $area_id]);
        }

        return $query;
    }

    /**Devuelve las facturas pendientes por cobrar*/
    public function actionGetFacturasPendientesAjax()
    {
        if (!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        $query = $this->getFacturasPendQuery();

        $totalRows = $query->count();
        $filtered = $totalRows;

        //Paginador
        if (isset($_POST["length"]) && $_POST["length"] != -1) {
            $query->limit($_POST["length"])->offset($_POST["start"]);
        }

        $result = $query->asArray()->all();

        $data = array();
        $i = 0;
        foreach ($result as $orden) {
            $sub_array = array();
            $sub_array[] = $orden['codigo'];
            $sub_array[] = $orden['fecha_iniciada'];
            $sub_array[] = $orden['cliente'];
            $sub_array[] = Html::a('<span class="glyphicon glyphicon-search"></span>', ['view', 'id' => $orden['id']], ['class' => 'btn btn-default btn-xs']);
            $data[] = $sub_array;
            $i++;
        }

        $draw = 0;
        if (isset($_POST["draw"]))
            $draw = intval($_POST["draw"]);
        $output = array(
            "draw"       =>  $draw,
            "recordsTotal"   =>  $totalRows,
            "recordsFiltered"  =>  $filtered,
            "data"       =>  $data
        );

        return json_encode($output);
    }

    public function actionFacturasCount()
    {
        if (!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        $query = $this->getFacturasPendQuery();

        return json_encode($query->count());
    }

    /**
     * Finds the OrdenServicio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrdenServicio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function findModel($id)
    {
        /* if (($model = OrdenServicio::findOne($id)) !== null) {
            return $model;
        }*/

        if (($model = OrdenServicio::findOne(['id' => $id, 'eliminado' => false])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function getOrdenSerie()
    {
        return OrdenSeries::find()->having(['tipo' => 'SERVICIOS'])->max('valor');  //orderBy(['valor' => 'DESC'])->DasArray()->all(), 'id', 'nombre');
    }
}
