<?php

namespace app\controllers\admin;

use Yii;
use app\models\Manager;
use app\models\ManagerSearch;
use app\controllers\ControllerMain;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class ManagerController extends ControllerMain
{
    public $defaultAction='list';
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
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
                    'ajaxadd'=>['post'],
                    'ajaxchange'=>['post']
                ],
            ],
        ];
    }
    public function actions(){
        return[
            'ajaxadd'=>'app\controllers\admin\ManagerActions\Ajaxadd',
            'ajaxchange'=>'app\controllers\admin\ManagerActions\Ajaxchange',
        ];
    }

    /**
     * Lists all Manager models.
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new ManagerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'vName'=>'Сторонние менеджеры'
        ]);
    }

    /**
     * Displays a single Manager model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetails($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'vName'=>'менеджер',
            'backName'=>'Сторонние менеджеры'
        ]);
    }
    
    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionChange($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['details', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'vName'=>'Изменить параметры менеджера',
                'backName'=>'Сторонние менеджеры'
            ]);
        }
    }

    /**
     * Deletes an existing Manager model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $this->findModel($id)->delete();
        if (!\yii::$app->request->isAjax)
            return $this->redirect(['list']);
        else{
             \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
             return [
                 'status'=>'ok'
             ];
        }
    }

    /**
     * Finds the Manager model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Manager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
