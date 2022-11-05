<?php

namespace backend\modules\Facturacion\controllers;

use Yii;
use backend\modules\Facturacion\models\OrdenVenta;
use backend\modules\Facturacion\models\OrdenVentaSearch;
use backend\modules\Facturacion\models\form\OrdenVentaForm;
use backend\modules\Facturacion\models\Cliente;
use backend\modules\Facturacion\controllers\CeoInfosController;
use backend\modules\Nomencladores\models\EstadoOrden;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Seguridad\controllers\TrazasVentasController;
use backend\modules\Seguridad\controllers\TrazasProductosController;
use backend\modules\Facturacion\controllers\MonedasController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use backend\modules\Facturacion\ESTADOS;
use backend\modules\Facturacion\models\OrdenSeries;
use kartik\mpdf\Pdf;
use yii\helpers\Html;

/**
 * OrdenventasController implements the CRUD actions for OrdenVenta model.
 */
class OrdenventasController extends Controller
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
                            'facturar-orden', 'imprimir-factura', 'cancelar-orden', 'set-cobrada',
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
        ];
    }

    /**
     * Lists all OrdenVenta models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrdenVentaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrdenVenta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $msgpart = "";

            if (isset(Yii::$app->request->post()['OrdenVenta']['cliente'])) {
                $model->cliente_id = Yii::$app->request->post()['OrdenVenta']['cliente'];
                if (strlen($msgpart) <= 0)
                    $msgpart = "cliente";
            }

            if ($model->save()) {
                TrazasVentasController::insert("Actualizar " . $msgpart . " de la orden, código: " . $model->codigo, $model->id);
                Yii::$app->getSession()->setFlash('success', 'La orden fue actualizada satisfactoriamente');
            }
        }

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
     * Creates a new OrdenVenta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrdenVentaForm();
        $model->ordenVenta = new OrdenVenta;
        $model->ordenVenta->loadDefaultValues();
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post()) {
            $serie = $this->getOrdenSerie();
            $showWarning = false;

            if ($serie !== $model->ordenVenta->serie) {
                $showWarning = true;
                $date = date('Y');
                $model->ordenVenta->codigo = $date . '/' . sprintf("%06d", $serie);
            }

            $model->ordenVenta->moneda_id = 2;  //CUP

            if ($model->saveAll()) {
                Yii::$app->getSession()->setFlash('success', 'La orden fue creada satisfactoriamente');
                if ($showWarning)
                    Yii::$app->getSession()->setFlash('info', 'El código de la orden se reestableció a : ' . $model->ordenVenta->codigo);

                return $this->redirect(['update', 'id' => $model->ordenVenta->id]);
            }
        }

        /* if (Yii::$app->request->post() && $model->saveAll()) {
            Yii::$app->getSession()->setFlash('success', 'La orden fue creada satisfactoriamente');
            
            return $this->redirect(['update', 'id' => $model->ordenVenta->id]);
        }*/

        $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado' => false])->asArray()->all(), 'id', 'nombre');
        $serie = $this->getOrdenSerie();
        $areas = ArrayHelper::map(Area::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'nombre');

        if (($m_estado = EstadoOrden::findOne(['estado' => ESTADOS::ABIERTO])) !== null) {
            return $this->render('create', [
                'model' => $model, 'clientes' => $clientes,
                'estado_id' => $m_estado->id,
                'area_id' => Yii::$app->session->get('area'), 'serie' => $serie, 'areas' => $areas
            ]);
        }
    }

    /**
     * Updates an existing OrdenVenta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = new OrdenVentaForm();
        $model->ordenVenta = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->saveAll()) {
            Yii::$app->getSession()->setFlash('success', 'La orden fue actualizada satisfactoriamente');
            return $this->redirect(['view', 'id' => $model->ordenVenta->id]);
        }

        if ($model->ordenVenta->estadoOrden->estado == ESTADOS::ABIERTO) {
            $clientes = ArrayHelper::map(Cliente::find()->having(['eliminado' => false])->asArray()->all(), 'id', 'nombre');
            $areas = ArrayHelper::map(Area::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'nombre');

            return $this->render('update', ['model' => $model, 'clientes' => $clientes, 'areas' => $areas]);
        } else {
            return $this->render('view', ['model' => $model->ordenVenta]);
        }
    }

    /** Facturar una orden. El estado resultante depende de la variable COBRADO */
    public function actionFacturarOrden($ordenId, $cobrado = false)
    {
        if (Yii::$app->request->isPost) {
            $ordenVenta = $this->findModel($ordenId);

            if ($ordenVenta != null) {
                $state = ESTADOS::FACTURADO;
                if ($cobrado == true) {
                    $state = ESTADOS::COBRADO;
                }

                if (($m_estado = EstadoOrden::findOne(['estado' => $state])) !== null) {
                    $ordenVenta->estado_orden_id = $m_estado->id;

                    if ($m_estado->estado == ESTADOS::COBRADO) {
                        $ordenVenta->fecha_cobrada = date('Y-m-d');

                        if ($ordenVenta->fecha_facturada == null)
                            $ordenVenta->fecha_facturada = date('Y-m-d');   //Facturada y cobrada
                    } else {   //Facturado
                        $ordenVenta->fecha_facturada = date('Y-m-d');
                    }

                    $transaction = Yii::$app->db->beginTransaction();

                    //Validar que exista el producto en almacen antes de facturar
                    $prodsList = $ordenVenta->productosOrdenVentas;
                    $sinExistencias = [];
                    foreach ($prodsList as $prodOrden) {
                        if ($prodOrden->cantidad > $prodOrden->producto->existencia) {
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
                        if ($ordenVenta->save()) {
                            //Rebajar de inventario y de la reserva
                            $dupProducts = [];   /* Como en las ordenes de ventas pueden existir productos repetidos, a la hora de rebajar 
                                                    de la existencia en almacen hay que ir almacenando la ultima rebajada del producto repetido anterior, 
                                                    ya que al acceder a $prodOrden->producto->existencia esto devuelve la existencia del producto en la BD,
                                                    no la que se ha ido restando, ya que se encuentra dentro de una transaccion.
                                                    Lo mismo pasa con la cantidad reservada */
                            $existenciaIni = 0;
                            $existencia = 0;
                            $cant_reservada = 0;
                            $pid = null;
                            foreach ($prodsList as $prodOrden) {
                                $existenciaIni = $prodOrden->producto->existencia;
                                $existencia = $existenciaIni;
                                $cant_reservada = $prodOrden->producto->cant_reservada;
                                $pid = $prodOrden->producto->id;

                                if (isset($dupProducts[$pid])) {
                                    $existencia = $dupProducts[$pid]['existencia'];
                                    $cant_reservada = $dupProducts[$pid]['reserva'];
                                }

                                $prodOrden->producto->existencia = round(($existencia - $prodOrden->cantidad), 3);
                                $prodOrden->producto->cant_reservada = round(($cant_reservada - $prodOrden->cantidad), 3);

                                $dupProducts[$pid]['existencia'] = $prodOrden->producto->existencia;
                                $dupProducts[$pid]['reserva'] = $prodOrden->producto->cant_reservada;

                                if (!$prodOrden->producto->save()) {
                                    $errors = "  Error guardando producto <b>" . $prodOrden->producto->codigo . "</b>";
                                    foreach ($prodOrden->producto->getErrors() as $e) {
                                        foreach ($e as $se) {
                                            $errors .= '<br>   -' . substr($se, strpos($se, '\"'), strlen($se));
                                        }
                                    }

                                    Yii::$app->getSession()->setFlash('danger', 'Error rebajando del inventario. No se realizó la operación' . '<br>' . $errors);
                                    $transaction->rollBack();
                                    return $this->redirect(['view', 'id' => $ordenId]);
                                }

                                TrazasProductosController::insert("Reajuste de inventario por facturación de la orden (" . $ordenVenta->codigo . "). Existencia inicial: " . $existenciaIni . " .Existencia final: " . $prodOrden->producto->existencia, $prodOrden->producto->id);
                            }

                            $traza = "Facturar orden: ";
                            if ($cobrado == true)
                                $traza = "Facturar y cobrar orden: ";
                            TrazasVentasController::insert($traza . $ordenVenta->codigo, $ordenVenta->id);

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
                    TrazasVentasController::insert("Cobrar orden: " . $orden->codigo, $orden->id);

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
                $prodsList = $orden->productosOrdenVentas;
                foreach ($prodsList as $prodOrden) {
                    //$err = 'Prod:    '.$prodOrden->producto->id.'   cant_reservada: '.$prodOrden->producto->cant_reservada.'   Cantidad:'.$prodOrden->cantidad;
                    $prodOrden->producto->cant_reservada = ($prodOrden->producto->cant_reservada - $prodOrden->cantidad);

                    if (!$prodOrden->producto->save()) {
                        Yii::$app->getSession()->setFlash('danger', 'Error del sistema. No se realizó la operación de Cancelar');
                        $transaction->rollBack();
                        return $this->redirect(['view', 'id' => $ordenId]);
                    }
                }
            } else
            if ($orden->estadoOrden->estado != ESTADOS::CANCELADO) {  //No se supone q se cancele una orden cancelada pero se verifica para mayor seguridad
                $prodsList = $orden->productosOrdenVentas;
                foreach ($prodsList as $prodOrden) {
                    $existenciaIni = $prodOrden->producto->existencia;
                    $prodOrden->producto->existencia = ($prodOrden->producto->existencia + $prodOrden->cantidad);

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
                    TrazasVentasController::insert("Cancelar orden: " . $orden->codigo, $orden->id);

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

            if (count($model->productosOrdenVentas) <= 0) {
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
            $firma =  ($printfirma == '1'/* && in_array(UserRole::ROL_JEFE_AREA, $keys)*/);
            if ($firma == true)
                $methods['SetWatermarkImage'] = ['/InvFactServices/backend/web/images/Fa1.png', 3, array(30, 20), array(130, 240)];

            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                // set to use core fonts only
                'mode' => Pdf::MODE_CORE,
                // A4 paper format
                'format' => Pdf::FORMAT_LETTER,
                // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
                // stream to browser inline
                'destination' => Pdf::DEST_BROWSER,
                // your html content input
                'content' => $content,
                // format content from your own css file if needed or use the
                // enhanced bootstrap css built by Krajee for mPDF formatting
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                // any css to be embedded if required
                'cssInline' => '.kv-heading-1{font-size:18px}',
                // set mPDF properties on the fly
                'options' => ['title' => 'Krajee Report Title', 'showWatermarkImage' => $firma],
                // call mPDF methods on the fly
                'methods' => $methods,
                /* [
                    //'SetHeader'=>['Oferta de Ventas'],
                    'SetFooter'=> $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo]),   
                   // 'SetWatermarkText' => ['f'],
                    'SetWatermarkImage' => ['/InvFactServices/backend/web/images/Fa1.png', 1, '', array(140, 240)],
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

            if (count($model->productosOrdenVentas) <= 0) {
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
                   // 'SetHeader'=>['Factura de ventas'],
                    'SetFooter'=> $this->renderPartial('_print-firma', ['ceoInfo' => $ceoInfo, 'printfirma' => $printfirma]), //['{PAGENO}'],
                ]*/
            ]);

            // file_put_contents('E:\\e.txt', serialize($pdf->getApi()->packTableData));
            /* $pdf->getApi()->simpleTables = true;
            $pdf->getApi()->packTableData = true;
            $pdf->getApi()->shrink_tables_to_fit = 1;*/
            //file_put_contents('E:\\e.txt', serialize($pdf->getApi()->packTableData));

            $pdf->filename = "Factura " . $model->codigo . ".pdf";
            return $pdf->render();
        } else {
            Yii::$app->getSession()->setFlash('danger', 'Error obteniendo los datos de la Orden');
            return false;
        }
    }

    /**
     * Deletes an existing OrdenVenta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();
        $model = $this->findModel($id);
        $model->eliminado = true;
        $model->save();

        return $this->redirect(['index']);
    }

    private function getOfertasAjaxQuery()
    {
        $query = OrdenVenta::find();
        $query->joinWith(['cliente', 'estadoOrden']);
        $query->andFilterWhere(['like', 'estado_orden.estado', ESTADOS::ABIERTO]);
        $query->andFilterWhere(['=', 'orden_ventas.eliminado', false]);
        $query->orderBy(['fecha_iniciada' => SORT_DESC]);

        $area_id = -1;
        if (Yii::$app->session->has('area')) {
            $area_id = Yii::$app->session->get('area');
            if ($area_id > 0)
                $query->andFilterWhere(['=', 'orden_ventas.area_id', $area_id]);
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
            $sub_array[] = $orden['cliente']['nombre'];
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
        $query = OrdenVenta::find();
        $query->joinWith(['cliente', 'estadoOrden']);
        $query->andFilterWhere(['like', 'estado_orden.estado', ESTADOS::FACTURADO]);
        $query->andFilterWhere(['=', 'orden_ventas.eliminado', false]);
        $query->orderBy(['fecha_iniciada' => SORT_DESC]);

        $area_id = -1;
        if (Yii::$app->session->has('area')) {
            $area_id = Yii::$app->session->get('area');
            if ($area_id > 0)
                $query->andFilterWhere(['=', 'orden_ventas.area_id', $area_id]);
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
            $sub_array[] = $orden['cliente']['nombre'];
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
     * Finds the OrdenVenta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrdenVenta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /*if (($model = OrdenVenta::findOne($id)) !== null) {
            return $model;
        }*/
        if (($model = OrdenVenta::findOne(['id' => $id, 'eliminado' => false])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function getOrdenSerie()
    {
        return OrdenSeries::find()->having(['tipo' => 'VENTAS'])->max('valor');
    }
}
