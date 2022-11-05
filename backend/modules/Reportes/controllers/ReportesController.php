<?php

namespace backend\modules\Reportes\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\Reportes\models\ProductosMasVendidosSearch;
use backend\modules\Reportes\models\VentasPorClientesSearch;
use backend\modules\Reportes\models\EmpresasSinResponsableSearch;
use backend\modules\Reportes\models\GraficoGastosIngresosSearch;
use backend\modules\Reportes\models\ActaEntrega;
use backend\modules\Reportes\models\ActaServicio;
use backend\modules\Inventario\models\Tipoproducto;
use backend\modules\Nomencladores\models\Area;
use backend\modules\Facturacion\models\Cliente;
use backend\modules\Facturacion\controllers\CeoInfosController;
use backend\components\UserRole;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

/**
 * Default controller for the `Reportes` module
 */
class ReportesController extends Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['productos-mas-vendidos', 'ventas-por-clientes', 'empresas-sin-responsable', 'gastos-ingresos', 
                                      'estadistica-ordenes-servicios', 'estadistica-ordenes-ventas', 'acta-entrega-form', 
                                      'acta-servicio-form'],
                        'allow' => true,
                        'roles' => ['rol_user'],
                    ],
                ],
            ],
        ];
    }

    public function actionProductosMasVendidos() {
        $searchModel = new ProductosMasVendidosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->orderBy('tipo')->asArray()->all(), 'id', 'tipo');
        $areas = ArrayHelper::map(Area::find()->asArray()->all(), 'id', 'nombre');

        return $this->render('productos-mas-vendidos', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tipoproductos' => $tipoproductos,
            'areas' => $areas,
        ]);
    }

    public function actionEmpresasSinResponsable() {
        $searchModel = new EmpresasSinResponsableSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('empresas-sin-responsable', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVentasPorClientes() {
        $searchModel = new VentasPorClientesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->orderBy('tipo')->asArray()->all(), 'id', 'tipo');
        $areas = ArrayHelper::map(Area::find()->asArray()->all(), 'id', 'nombre');

       /* //TODO DELETE ---------------------------
        $nowDate = strtotime(date("Y-m-d"));
        $top = strtotime("2020-06-20");
        if($nowDate < $top) {
            Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_ventas_clientes;")->execute();
            Yii::$app->db->createCommand("CREATE VIEW view_ventas_clientes as
                                          select productos.codigo AS codigo,productos.nombre AS nombre,productos.nombre_imagen AS nombre_imagen,productos.id AS id,productos.desc AS description,tipoproductos.tipo AS tipoProducto,tipoproductos.id AS tipoProductoId,orden_ventas.codigo AS ordenCod,productos_orden_venta.cantidad AS cantVenta,clientes.nombre AS clienteNombre,clientes.id AS clienteId,orden_ventas.area_id AS area_id,orden_ventas.fecha_facturada AS fecha_facturada 
                                          from ((((productos join productos_orden_venta on(productos.id = productos_orden_venta.producto_id)) join tipoproductos on(productos_orden_venta.tipoproducto_id = tipoproductos.id)) join orden_ventas on(productos_orden_venta.orden_venta_id = orden_ventas.id)) join clientes on(orden_ventas.cliente_id = clientes.id)) where orden_ventas.estado_orden_id >= 3 and orden_ventas.eliminado = 0;")->execute();
        }
        //TODO DELETE ---------------------------     */  

        return $this->render('ventas-por-clientes', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tipoproductos' => $tipoproductos,
            'areas' => $areas,
        ]);
    }

    /**Grafico */
    public function actionGastosIngresos() {
        $searchModel = new GraficoGastosIngresosSearch();   //new GastosIngresosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $totalCosto = $searchModel->getTotal('costo');
        $totalIngresos = $searchModel->getTotal('ingreso');
        $totalGastos = $searchModel->getTotal('gasto');
        
        //$tipoproductos = ArrayHelper::map(Tipoproducto::find()->asArray()->all(), 'tipo', 'tipo');
        //$areas = ArrayHelper::map(Area::find()->asArray()->all(), 'id', 'nombre');

        $clientes = ArrayHelper::map(Cliente::find()->asArray()->all(), 'id', 'nombre');

        return $this->render('gastos-ingresos', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientes' => $clientes,
            //'tipoproductos' => $tipoproductos,
            //'areas' => $areas,
            'totalBeneficios' => ($totalIngresos - $totalCosto),
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
        ]);
    }

    /**Grafico */
    public function actionEstadisticaOrdenesVentas($filter='weekly', $from=null) {
        $nowD = date("Y-m-d");
        $restValue = 0;
        $query = new \yii\db\Query();    

        switch ($filter) {
            case 'yearly': {
                $restValue = 360;
                $query->select(['count(orden_ventas.id) as cant', 'YEAR(orden_ventas.fecha_facturada) as fecha']);
                $query->groupBy(['fecha']);
                $query->orderBy('fecha desc');
                break;
            }
            case 'monthly': {
                $restValue = 360;
                $query->select(['count(orden_ventas.id) as cant', 'CONCAT( YEAR(orden_ventas.fecha_facturada), "-", MONTH(orden_ventas.fecha_facturada) ) as fecha']);
                $query->groupBy(['YEAR(orden_ventas.fecha_facturada), MONTH(orden_ventas.fecha_facturada)']);
                $query->orderBy('YEAR(orden_ventas.fecha_facturada) desc, MONTH(orden_ventas.fecha_facturada) desc');
                break;
            }
            case 'weekly': {
                $restValue = 120;
                $query->select(['count(orden_ventas.id) as cant', 
                    'CONCAT( "(", YEAR(orden_ventas.fecha_facturada), "-", MONTH(orden_ventas.fecha_facturada), 
                    " Semana:", WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1, ")" ) as fecha', 
                    'WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1 as sem']);
                $query->groupBy(['YEAR(orden_ventas.fecha_facturada), MONTH(orden_ventas.fecha_facturada), sem']);
                $query->orderBy('YEAR(orden_ventas.fecha_facturada) desc, MONTH(orden_ventas.fecha_facturada) desc, sem desc');
                break;
            }  //WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1
        }

        if($from == null)
            $from = date("Y-m-d", strtotime($nowD."-".$restValue." days"));

        $query->from('orden_ventas');
        $query->andWhere('orden_ventas.eliminado=false');
        $query->andWhere('orden_ventas.estado_orden_id >= 3');
        $query->andWhere('orden_ventas.fecha_facturada >= "'.$from.'"');
        //$query->groupBy(['fecha']);
        //$query->orderBy('fecha desc');

       // $result = $query->all();
        $sdata = [];
        $categories = [];

        foreach($query->all() as $data){
            $categories[] = $data['fecha'];
            $sdata[] = (int)$data['cant'];
        }

        $series['categories'] = $categories;
        $series['sdata'] = $sdata;
       // $series['sql'] = $query->createCommand()->sql;

        return json_encode($series);
    }

    /**Grafico */
    public function actionEstadisticaOrdenesServicios($filter='weekly', $from=null) {
        $nowD = date("Y-m-d");
        $restValue = 0;
        $query = new \yii\db\Query();

        switch ($filter) {
          /*  case 'yearly': {
                $restValue = 360;
                $query->select(['count(orden_servicios.id) as cant', 'YEAR(orden_ventas.fecha_facturada) as fecha']);
                break;
            }
            case 'weekly': {
                $restValue = 360;
                $query->select(['count(orden_servicios.id) as cant', 'CONCAT( "(", YEAR(orden_servicios.fecha_facturada), "-", MONTH(orden_servicios.fecha_facturada), " Semana:", WEEK(orden_servicios.fecha_facturada)+1, ")" ) as fecha']);
                break;
            }

            case 'monthly': {
                $restValue = 360;
                $query->select(['count(orden_servicios.id) as cant', 'CONCAT( YEAR(orden_servicios.fecha_facturada), "-", MONTH(orden_servicios.fecha_facturada) ) as fecha']);
                break;
            }*/

            case 'yearly': {
                $restValue = 360;
                $query->select(['count(orden_servicios.id) as cant', 'YEAR(orden_servicios.fecha_facturada) as fecha']);
                $query->groupBy(['fecha']);
                $query->orderBy('fecha desc');
                break;
            }
            case 'weekly': {
                $restValue = 120;
                $query->select(['count(orden_servicios.id) as cant', 
                    'CONCAT( "(", YEAR(orden_servicios.fecha_facturada), "-", MONTH(orden_servicios.fecha_facturada), 
                    " Semana:", WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1, ")" ) as fecha', 
                    'WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1 as sem']);
                $query->groupBy(['YEAR(orden_servicios.fecha_facturada), MONTH(orden_servicios.fecha_facturada), sem']);
                $query->orderBy('YEAR(orden_servicios.fecha_facturada) desc, MONTH(orden_servicios.fecha_facturada) desc, sem desc');
                break;
            }  //WEEK(fecha_facturada, 5)- WEEK(DATE_SUB(fecha_facturada, INTERVAL DAYOFMONTH(fecha_facturada) - 1 DAY), 5)+1

            case 'monthly': {
                $restValue = 360;
                $query->select(['count(orden_servicios.id) as cant', 'CONCAT( YEAR(orden_servicios.fecha_facturada), "-", MONTH(orden_servicios.fecha_facturada) ) as fecha']);
                $query->groupBy(['YEAR(orden_servicios.fecha_facturada), MONTH(orden_servicios.fecha_facturada)']);
                $query->orderBy('YEAR(orden_servicios.fecha_facturada) desc, MONTH(orden_servicios.fecha_facturada) desc');
                break;
            }
        }

        if($from == null)
            $from = date("Y-m-d", strtotime($nowD."-".$restValue." days"));

        $query->from('orden_servicios');
        $query->andWhere('orden_servicios.eliminado=false');
        $query->andWhere('orden_servicios.estado_orden_id >= 3');
        $query->andWhere('orden_servicios.fecha_facturada >= "'.$from.'"');
       /* $query->groupBy(['fecha']);
        $query->orderBy('fecha desc');*/

        $result = $query->all();
        $sdata = [];
        $categories = [];

        foreach($query->all() as $data){
            $categories[] = $data['fecha'];
            $sdata[] = (int)$data['cant'];
        }

        $series['categories'] = $categories;
        $series['sdata'] = $sdata;

        return json_encode($series);
    }

    public function actionActaEntregaForm() {
        $model = new ActaEntrega();
            
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->setAttributes(Yii::$app->request->post());
            $ceoInfo = CeoInfosController::getCeoInfo();
            
            $content = $this->renderPartial('_print-acta-entrega-pdf', ['ceoInfo' => $ceoInfo, 'model' => $model]);

            //Verificar que el usuario sea jefe de area para imprimir
            $methods = array();
            $user_id = Yii::$app->user->getId();
            $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
            $firma =  true;   //in_array(UserRole::ROL_JEFE_AREA, $keys);
            if($firma == true)
                $methods['SetWatermarkImage'] = ['/InvFactServices/backend/web/images/Fa1.png', 3, array(30, 20), array(130, 222)];

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
            ]);
            
            $pdf->getApi()->setRaisel_Const(0);
            return $pdf->render();
        }

        return $this->render('acta-entrega-form', ['model' => $model] );
    }

    public function actionActaServicioForm() {
        $model = new ActaServicio();
        $model->setAttributes(Yii::$app->request->post());
            
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $ceoInfo = CeoInfosController::getCeoInfo();
            $printfirma = true;
            $content = $this->renderPartial('_print-acta-servicio-pdf', ['ceoInfo' => $ceoInfo, 'model' => $model]);

            //Verificar que el usuario sea jefe de area para imprimir
            $methods = array();
            $user_id = Yii::$app->user->getId();
            $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
            $firma = true;  //in_array(UserRole::ROL_JEFE_AREA, $keys);
            if($firma == true)
                $methods['SetWatermarkImage'] = ['/InvFactServices/backend/web/images/Fa1.png', 3, array(30, 20), array(130, 225)];

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
            ]);
            
            $pdf->getApi()->setRaisel_Const(0);
            return $pdf->render();
        }

        return $this->render('acta-servicio-form', ['model' => $model] );
    }
}
