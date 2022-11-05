<?php

namespace backend\modules\Migracion\controllers;

use yii\web\Controller;
use Yii;

/**
 * Default controller for the `migracion` module
 */
class DefaultController extends Controller
{
    //private $status = 1;

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

  /*  public function actionImportBd() {
        if(Yii::$app->request->isPost) {
            return $this->render('index', ['action'=>'import']);
        }
        else
            return $this->render('index');
    }*/

    public function actionTest() {
        $dbsqlserver = Yii::$app->dbsqlserver;

        $categories = $dbsqlserver->createCommand('select * from category limit 10')->queryAll();

        return $this->render('index', ['dbsqlserver'=>$dbsqlserver]);
    }

    public function actionImportAjax() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');
        
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $t = 10000;
       // ob_implicit_flush(true);
        //echo "1: Preparando tablas..."
        
        $cont = "";
        for($i=0; $i < $t; $i++) {
            $cont .= $i;
        }
        header('Content-length: '.strlen($cont));

        /*$session = Yii::$app->session;
        $session->open();*/

        for($i=0; $i < $t; $i++) {
            //ob_start();
           // echo  "-".($i*100)/$t;

          //  echo $i;

         //   flush();
          //  ob_flush();
            
            
          //  sleep(1);
          //  ob_end_clean();
            
           // ob_end_flush();

            $db->createCommand()->insert('migration', ['version'=>"v".$i, 'apply_time'=>date("Y/m/d ss")])->execute();
            //echo json_encode(($i*100)/$t);
            echo $i;
            //ob_clean();

           /* if($i==100 || $i==1000 || $i==2000 || $i==10000)
                $session->set('status', $i);*/
        }

        $transaction->commit();

      //  return json_encode("");
    }

    public function actionGetCountAjax() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');
        
        return json_encode(10000000);
    }

    public function actionGetStatus() {
        if(!Yii::$app->request->isPost)
            throw new NotFoundHttpException('La página solicitada no existe');
        
        return json_encode(Yii::$app->session->get('status'));
    }
}
