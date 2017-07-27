<?php
namespace app\controllers\zakazi;

use Yii;

use app\controllers\ControllerMain;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\Materials;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
/**
 * FirmsController implements the CRUD actions for Firms model.
 */
class MaterialController extends ControllerMain
{
    public $defaultAction='list';
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
        Url::remember();
        return $rVal;
    }
    public function actionList($tblName=false){
        if ($tblName){
            $model=new Materials();
            $model->rules();
            $dataProvider = new ActiveDataProvider([
                'query' => $model->find(),
            ]);
        }else{
            $model=null;
            $dataProvider=null;
        }
        $upd=ArrayHelper::getValue(\Yii::$app->request->get(),'update');
        
        if ($upd){
            \yii\helpers\VarDumper::dump($upd,10,true);\Yii::$app->end();
            return $this->redirect(Url::to(['list','tblName'=>$tblName]));
        }else{
            return $this->render('list',['tblName'=>$tblName,'model'=>$model,'dataProvider'=>$dataProvider]);
        }
    }
    
    public function actionAdd($tblName=false){
        if ($tblName){
            $model=new Materials();
            return $this->render('add',['model'=>$model,'tblName'=>$tblName]);
        }else{
            return $this->render('list',['tblName'=>$tblName]);
        }
    }
    
    public function actionAjaxList(){
        
    }
}