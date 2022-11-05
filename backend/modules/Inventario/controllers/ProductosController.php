<?php

namespace backend\modules\Inventario\controllers;

use Yii;
use backend\modules\Inventario\models\Producto;
//use backend\modules\Inventario\models\Almacen;
use backend\modules\Inventario\models\ProductoSearch;
use backend\modules\Inventario\models\Tipoproducto;
use backend\modules\Nomencladores\models\UnidadMedida;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Status;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use backend\modules\seguridad\controllers\TrazasProductosController;
use yii\helpers\Html;
use kartik\mpdf\Pdf;

/**
 * ProductosController implements the CRUD actions for Producto model.
 */
class ProductosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors() {
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
                        'actions' => ['create', 'update', 'delete', 'index', 'view', 
                                      'existencias', 'create-via-ajax', 'imprimir-productos', 'imprimir-existencias'],
                        'roles' => ['rol_inventario'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['getajaxproducts', 'productos-bajo-stock-ajax', 'stock-compare'], 
                        //'actions' => ['getajaxproducts'=>['POST']],
                        'roles' => ['rol_user'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Producto models.
     * @return mixed
     */
    public function actionIndex() {
       /* if(isset(Yii::$app->request->queryParams['almacen'])) {
            $session = Yii::$app->session;
            $session->set('almacen', Yii::$app->request->queryParams['almacen']);*/

            $searchModel = new ProductoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            
          //Establecer el ultimo valor de los filtros, ya que si el timeout del PJAX se cumple los componentes se quedan en blanco  
            $orvalue = "";
            if(isset(Yii::$app->request->queryParams['ProductoSearch']['or_value']))
                $orvalue = Yii::$app->request->queryParams['ProductoSearch']['or_value'];
            
            $tipoProdValue = "";
            if(isset(Yii::$app->request->queryParams['ProductoSearch']['tipoProducto']))
                $tipoProdValue = Yii::$app->request->queryParams['ProductoSearch']['tipoProducto'];

            $tipoproductos = ArrayHelper::map(Tipoproducto::find()->where(['eliminado' => false])->orderBy('tipo')->asArray()->all(), 'id', 'tipo');

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'orvalue' => $orvalue, 
                'tipoProdValue' => $tipoProdValue, 
                'tipoproductos' => $tipoproductos, 
                //'almacen' => Yii::$app->request->queryParams['almacen'],
            ]);
    }

    public function actionExistencias() {
        $searchModel = new ProductoSearch();
       // $params = Yii::$app->request->queryParams;
      //  $params['ProductoSearch']['existencia'] = '0';
       // $params['ProductoSearch']['operator'] = '>';
        
        //$dataProvider = $searchModel->search($params);
        //$params = ['ProductoSearch' => ['existencia' => '0', 'operator' => '>'] ];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $orvalue = "";
        if(isset(Yii::$app->request->queryParams['ProductoSearch']['or_value']))
            $orvalue = Yii::$app->request->queryParams['ProductoSearch']['or_value'];

        $tipoProdValue = "";
        if(isset(Yii::$app->request->queryParams['ProductoSearch']['tipoProducto']))
            $tipoProdValue = Yii::$app->request->queryParams['ProductoSearch']['tipoProducto'];

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->where(['eliminado' => false])->orderBy('tipo')->asArray()->all(), 'id', 'tipo');

        return $this->render('existencias', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'orvalue' => $orvalue, 
            'tipoProdValue' => $tipoProdValue, 
            'tipoproductos' => $tipoproductos, 
        ]);
    }

    public function actionImprimirProductos($or_value=null, $tipoProdValue=null) {
        return $this->printInventario($or_value, $tipoProdValue);
    }

    private function printInventario($or_value=null, $tipoProdValue=null) {
        $searchModel = new ProductoSearch();
        $queryParams['ProductoSearch']['or_value'] = $or_value;
        $queryParams['ProductoSearch']['tipoProducto'] = $tipoProdValue;
        $searchModel->search($queryParams);
        $data = $searchModel->getQueryData();

        //ini_set('max_execution_time', '300');
        //ini_set("pcre.backtrack_limit", "2000000");
        $content = $this->renderPartial('_print-inventario', ['data' => $data]);
        
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_LETTER,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title',],
            'methods' => [
                // 'SetHeader'=>['Oferta de Servicios'],
                'SetFooter'=> ['{PAGENO}'],
            ],
        ]);

        $pdf->getApi()->setRaisel_Const(0);
        // Yii:$app->response->format = \yii\web\Response::FORMAT_RAW;
       
        return $pdf->render();
    }

    public function actionImprimirExistencias($or_value=null, $tipoProdValue=null) {
        return $this->printInventario($or_value, $tipoProdValue);  //El searchModel Marca la diferencia con el Index
    }

    /**
     * Displays a single Producto model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $model = $this->findModel($id);
       // $model->joinWith(['tipoproducto', 'unidadMedida']);

        return $this->render('view', ['model' => $model,]);
    }

    /**
     * Creates a new Producto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Producto();

        if ($model->load(Yii::$app->request->post())) {
            $basePath = Yii::$app->basePath.'\\web\\uploads\\';
            $model->imagefile = UploadedFile::getInstance($model, 'imagefile');
            
            if($model->imagefile) {
                $model->nombre_imagen = $model->id.$model->imagefile->baseName.'.'.$model->imagefile->extension;
                $uploadUrl = $basePath.$model->nombre_imagen;

                if($model->imagefile->saveAs($uploadUrl) && $model->save()) {
                    TrazasProductosController::insert("Nuevo producto: ".$model->codigo.". Existencia inicial: ".$model->existencia, $model->id);

                    Yii::$app->getSession()->setFlash('success', 'El producto se guardó satisfactoriamente');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
            else {
                if($model->save()) {
                    TrazasProductosController::insert("Nuevo producto: ".$model->codigo.". Existencia inicial: ".$model->existencia, $model->id);

                    Yii::$app->getSession()->setFlash('success', 'El producto se guardó satisfactoriamente');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'tipo');
        $unidad_medidas = ArrayHelper::map(UnidadMedida::find()->where(['eliminado'=> false])->asArray()->all(), 'id', 'unidad_medida');

       /* $session = Yii::$app->session;
        $almacen_id = $session->get('almacen');
        $almacen = $this->findAlmacen($almacen_id);*/

        return $this->render('create', ['model' => $model, 'tipoproductos' => $tipoproductos, 
                             'unidad_medidas' => $unidad_medidas,  ]);  //'almacen' => $almacen
    }

    /** Agrega un nuevo producto via AJAX. USado en las Ordenes de Compra */
    public function actionCreateViaAjax() {
        $model = new Producto();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $basePath = Yii::$app->basePath.'\\web\\uploads\\';
            $model->imagefile = UploadedFile::getInstance($model, 'imagefile');
            
            if($model->imagefile) {
                $model->nombre_imagen = $model->id.$model->imagefile->baseName.'.'.$model->imagefile->extension;
                $uploadUrl = $basePath.$model->nombre_imagen;

                if($model->imagefile->saveAs($uploadUrl) && $model->validate() && $model->save()) {
                    TrazasProductosController::insert("Nuevo producto: ".$model->codigo.". Existencia inicial: 0", $model->id);
                    return json_encode(array('status' => 'success', 'type' => 'success'));
                }
                else {
                    return json_encode(array('status' => 'error', 'type' => 'error', 'errors'=>$model->errors));
                }
            }
            else {
                if($model->validate() && $model->save()) {
                    TrazasProductosController::insert("Nuevo producto: ".$model->codigo.". Existencia inicial: 0", $model->id);
                    return json_encode(array('status' => 'success', 'type' => 'success'));
                }
                else {
                    return json_encode(array('status' => 'error', 'type' => 'error', 'errors'=>$model->errors));
                }
            }
        }

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'tipo');
        $unidad_medidas = ArrayHelper::map(UnidadMedida::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'unidad_medida');

        return $this->renderAjax('_create_ajax', [
            'model' => $model, 'tipoproductos' => $tipoproductos, 'unidad_medidas' => $unidad_medidas
        ]);
    }

    /**
     * Updates an existing Producto model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $oldImg = $model->nombre_imagen;
        $basePath = Yii::$app->basePath.'\\web\\uploads\\';
        $oldExistencia = $model->existencia;

        if ($model->load(Yii::$app->request->post())) {
            $model->imagefile = UploadedFile::getInstance($model, 'imagefile');
            
            if($model->imagefile) {
                $model->nombre_imagen = $model->id.$model->imagefile->baseName.'.'.$model->imagefile->extension;
                $uploadUrl = $basePath.$model->nombre_imagen;

                if( $oldImg!=null && file_exists($basePath.$oldImg) )
                    unlink($basePath.$oldImg);

                if($model->imagefile->saveAs($uploadUrl) && $model->save()) {
                    $this->saveEditTraza($oldExistencia, $model);

                    Yii::$app->getSession()->setFlash('success', 'El producto se guardó satisfactoriamente');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
            else {
                if($model->save()) {
                    if(!$model->nombre_imagen && $oldImg!=null && file_exists($basePath.$oldImg) )
                        unlink($basePath.$oldImg);

                    $this->saveEditTraza($oldExistencia, $model);

                    Yii::$app->getSession()->setFlash('success', 'El producto se guardó satisfactoriamente');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $tipoproductos = ArrayHelper::map(Tipoproducto::find()->where(['eliminado' => false])->asArray()->all(), 'id', 'tipo');
        $unidad_medidas = ArrayHelper::map(UnidadMedida::find()->where(['eliminado'=> false])->asArray()->all(), 'id', 'unidad_medida');

        return $this->render('update', ['model' => $model, 'tipoproductos' => $tipoproductos, 'unidad_medidas' => $unidad_medidas] );
    }

    private function saveEditTraza($oldExistencia, $model) {
        $trazaExistencia = "";
        if($oldExistencia != $model->existencia)
            $trazaExistencia = ". Reajuste de inventario de ".$oldExistencia." a ".$model->existencia.". Motivo: ".$model->trazacambio;
        
        return TrazasProductosController::insert("Editar producto: ".$model->codigo.$trazaExistencia, $model->id);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->eliminado=true;
        $model->save();
        TrazasProductosController::insert("Eliminado producto, Código: ".$model->codigo." , Nombre: ".$model->nombre, $model->id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Producto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Producto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        //if (!Status::find()->where(['id'=>$id, 'eliminado'=>false])->exists()) { // 
        if ( ($model = Producto::findOne(['id'=>$id]) ) !== null) {
             return $model;
        }

        throw new NotFoundHttpException('La página solicitada no existe');
    }

    /** Devuelve los productos para los datatables de OrdenServicios y OrdenVentas */
    public function actionGetajaxproducts() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        $showCosto = isset($_POST['showCosto']);

       /* if(!isset($_POST['almacen']))
            throw new NotFoundHttpException('Error obteniendo la información del ALMACÉN. Contacte al Adminsitrador del sistema');

        $almacen = $_POST['almacen'];*/

        //$totalRows = Producto::find()->having(['eliminado'=> false])->count();  //, 'almacen_id'=>$almacen
        //$filtered = $totalRows;

        $query = Producto::find()->having(['eliminado'=> false])->orderBy('nombre'); //, 'almacen_id'=>$almacen
        
        if(!isset($_POST['allExistences'])) //Si esta este parametro quiere decir que es una orden de compra, y por tanto se muestran todos aunque no tengan existencia
            $query->where(['>', 'existencia',  0]);
        
        //Filter
        if(isset($_POST['filterVal'])) {
            $query->andWhere(['OR', 
                ['like', 'nombre', trim($_POST['filterVal'])],
                ['like', 'codigo', trim($_POST['filterVal'])]
            ]);
          //  $query->orFilterWhere(['like', 'nombre', $_POST['filterVal']])
            //      ->orFilterWhere(['like', 'codigo', $_POST['filterVal']]);
        }

        $totalRows = $query->count(); 
        $filtered = $totalRows;

        //Paginador
        if(isset($_POST["length"]) && $_POST["length"] != -1) {
            $query->limit($_POST["length"])->offset($_POST["start"]);

           /* $lista_productos = ArrayHelper::map(Producto::find()
                                ->orFilterWhere(['like', 'nombre', $orvalue])
                                ->orFilterWhere(['like', 'codigo', $orvalue])
                                ->having(['eliminado'=> false])
                                ->orderBy('nombre')->limit($_POST["length"])->offset($_POST["start"])->asArray()->all(), 
                                'id', 'nombre', 'codigo');*/
        }

        //$lista_productos = ArrayHelper::map($query->asArray()->all(), 'id', 'nombre', 'codigo');
        $lista_productos = $query->asArray()->all();
       // print_r($query->asArray()->all());
        
        $data = array();
        $i=0;
        foreach($lista_productos as $prod) { 
            $sub_array = array();
            $sub_array[] = $prod['codigo'];
            $sub_array[] = $prod['nombre'];
            $sub_array[] = $prod['id'];
            $sub_array[] = $prod['existencia'];
            if($showCosto)
                $sub_array[] = $prod['costo'];
            else
                $sub_array[] = $prod['precio'];                
            $sub_array[] = '<a href="#" title="Seleccionar"><span class="glyphicon glyphicon-ok"><span></a>'; //'<button class="btn btn-link data_table_btn" type="button">Seleccionar</button>';
            $data[] = $sub_array;
            $i++;
        }

     /*   foreach($lista_productos as $cod=>$prod) { 
            $id = array_key_first($prod); 

            $sub_array = array();
            $sub_array[] = $cod;
            $sub_array[] = $prod[$id]; //Name
            $sub_array[] = $id;
            //$sub_array[] = $id;
            $sub_array[] = '<button class="btn btn-link data_table_btn" type="button">Seleccionar</button>';
            $data[] = $sub_array;
            $i++;
        }*/

        $draw = 0;
        if(isset($_POST["draw"]))
            $draw = intval($_POST["draw"]);
        $output = array(
            "draw"       =>  $draw,
            "recordsTotal"   =>  $totalRows,
            "recordsFiltered"  =>  $filtered,
            "data"       =>  $data
        );
        
        return json_encode($output);
       // return $this->render('getajaxproducts');
    }

    private function getBajoStockQuery() {
        $query = Producto::find()->having(['eliminado'=> false])->orderBy('nombre');
        $query->where('stock_minimo >= existencia');
        $query->where('existencia > 0');
        
        return $query;
    }
    /** Devuelve los productos que presentan un bajo stock en almacen. 
     * Utilizado en el DashBoard */
    public function actionProductosBajoStockAjax() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');

        //$totalRows = Producto::find()->having(['eliminado'=> false])->count();
        $query = $this->getBajoStockQuery();
        $totalRows = $query->count();
        $filtered = $totalRows;

      /*  $query = new Query();
        $query->select("codigo, nombre, existencia, stock_minimo")->from('productos');
        $totalRows = $query->count();
        $filtered = $totalRows;*/
       
        //Paginador
        if(isset($_POST["length"]) && $_POST["length"] != -1) {
            $query->limit($_POST["length"])->offset($_POST["start"]);
        }

        $lista_productos = $query->asArray()->all();
        
        $data = array();
        $i=0;
        foreach($lista_productos as $prod) { 
            $sub_array = array();
            $sub_array[] = $prod['codigo'];
            $sub_array[] = $prod['nombre'];
            $sub_array[] = $prod['existencia'];
            $sub_array[] = $prod['stock_minimo'];
            $sub_array[] = Html::a('<span class="glyphicon glyphicon-search"></span>', ['view', 'id'=>$prod['id']], ['class' => 'btn btn-default btn-xs']);
            $data[] = $sub_array;
            $i++;
        }

        $draw = 0;
        if(isset($_POST["draw"]))
            $draw = intval($_POST["draw"]);
        $output = array(
            "draw"       =>  $draw,
            "recordsTotal"   =>  $totalRows,
            "recordsFiltered"  =>  $filtered,
            "data"       =>  $data
        );
        
        return json_encode($output);
    }

    public function actionStockCompare() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');
        
        $query = $this->getBajoStockQuery();

        return json_encode($query->count());
    }
}
