<?php

namespace app\controllers\admin;

use yii;
use app\models\Firms;
use app\models\Zakaz;
use app\models\Manager;
use app\models\FirmsSearch;
use app\controllers\ControllerMain;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * FirmsController implements the CRUD actions for Firms model.
 */
class FirmsController extends ControllerMain
{
    public $defaultAction='list';
    public function init(){
        parent::init();
        $this->viewPath='@app/views/admin/firms';
    }
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
        Url::remember();
        if (!\yii::$app->request->isPost) \yii\helpers\Url::remember();
        $this->layout='main_2.php';
        return $rVal;
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'ajaxupdaterequest'=>['post']
                ],
                
            ],
        ];
    }
    public function actions(){
        return[
            'ajaxupdaterequest'=>'app\controllers\admin\FirmsActions\AjaxUpdateRequest',
        ];
    }

    /**
     * Lists all Firms models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new FirmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$dataProvider->pagination->pageSize=11;
        $tmpProv=$searchModel->search(Yii::$app->request->queryParams);
        $this->checkAndSetLastPage($dataProvider,$tmpProv->query,'`firms`.`id`');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'vName'=>'Названия фирм'
        ]);
    }

    /**
     * Displays a single Firms model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetails($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
                'vName'=>'фирма',
                'backName'=>'Названия фирм'
        ]);
    }

    /**
     * Creates a new Firms model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new Firms();
        $model->fone='{}';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //echo \yii\helpers\VarDumper::dumpAsString(Yii::$app->request->post(),10,true);Yii::$app->end();
            return $this->redirect(['list','lPage'=>true,'#'=>$model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'vName'=>'Добавить фирму',
                'backName'=>'Названия фирм'
            ]);
        }
    }

    /**
     * Updates an existing Firms model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionChange($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           // \yii\helpers\VarDumper::dump(Yii::$app->request->post(),10,true);Yii::$app->end();
            return $this->redirect($this->defaultBackUrlOption('details',['id' => $model->id]));
        } else {
            return $this->render('update', [
                'model' => $model,
                'vName'=>'Изменить параметры фирмы',
                'backName'=>'Названия фирм'
            ]);
        }
    }

    /**
     * Deletes an existing Firms model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $model=$this->findModel($id);//->delete();
        $tmp=ArrayHelper::map(Manager::find()->where(['firm_id'=>$id])->asArray()->all(),'id','name');
        if (count($tmp)){
            $tmpZakaz=ArrayHelper::map(Zakaz::find()->where(['customerManager'=>  array_keys($tmp)])->asArray()->all(),'id','name');
            if (count($tmpZakaz)){
                $opt=[
                    'nanagers'=>$tmp,
                    'zakaz'=>$tmpZakaz,
                    'model'=>$model,
                    'vName'=>'ошибка удаления',
                    'backName'=>'Названия фирм'
                ];
                $opt['page']=($tPage=yii::$app->request->get('page'))?$tPage:null;
//                if ($tPage=yii::$app->request->get('page')){
//                    $opt['page']=$tPage;
//                }
                return $this->render('deleteError',$opt);
            }
        }
        $model->delete();
        return $this->redirect($this->defaultBackUrlOption('list'));
    }

    /**
     * Finds the Firms model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Firms the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Firms::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
