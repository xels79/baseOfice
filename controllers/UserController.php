<?php

namespace app\controllers;

use Yii;
use app\models\TblUser;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;

/**
 * UserController implements the CRUD actions for TblUser model.
 */
class UserController extends ControllerMain
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    public function beforeAction($action)
{
    if (parent::beforeAction($action)) {
        

        if ($this->role=='logist'||($this->role=='moder'&&$action->id=='update')){
            if (!$tid=Yii::$app->request->get('id',false))
                $tid=Yii::$app->request->post('id',false);
            if ($tid&&$tid!=Yii::$app->user->identity->id){
                throw new ForbiddenHttpException('Недостаточно прав2');
            }
        }
        if ($action->id=='index'&&$this->role=='user'){
            throw new ForbiddenHttpException('Недостаточно прав2');
        }
//        $this->view->params['breadcrumbs'][]=[
//            'label'=>'На главную',
//            'url'=>Yii::$app->homeUrl
//        ];
        if ($action->id!='index'&& $this->role!='logist' && $this->role!='bugalter') $this->view->params['breadcrumbs'][]=['label' => 'Мэнеджеры', 'url' => ['index']];
        if (!\yii::$app->request->isPost) \yii\helpers\Url::remember();
        $this->layout='main_2.php';
        return true;
    } else {
        return false;
    }
}

    /**
     * Lists all TblUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TblUser::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblUser model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TblUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TblUser();
//        if (Yii::$app->request->isPost){
//            if ($hash=Yii::$app->request->post('nPassword'))
//                    $model->password=Yii::$app->getSecurity()->generatePasswordHash($nPassword);
//            if ($tmp=Yii::$app->request->post('nPassword')))
//        }
//        return $this->render('create', [
//            'model' => $model,
//        ]);
        if ($model->load(Yii::$app->request->post())){
            $nPass=Yii::$app->request->post('TblUser',[]);
            $nPass=isset($nPass['npassword'])?$nPass['npassword']:'';

                //if ($model->isNewRecord) {
                    //$model->password=\app\models\User::hashPassword($model->nPassword);
                    //Yii::info('password: '.$model->nPassword);
                    //echo '</br>pass :'.$nPass.'</br>post: '.$_POST['TblUser']['npassword'];Yii::$app->end();
                    $model->password=Yii::$app->getSecurity()->generatePasswordHash($nPass);
              //  }
                /*else{
                    if ($nPass!='')
                        $model->password=Yii::$app->getSecurity()->generatePasswordHash($nPass);
                    else{
                        $tmp=TblUser::findOne($model->id);
                        $model->password=$tmp->password;
                    }
                }*/
                if ($model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
        }
            return $this->render('create', [
                'model' => $model,
            ]);
        
        
    }

    /**
     * Updates an existing TblUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())){
            $nPass=Yii::$app->request->post('TblUser',[]);
            $nPass=isset($nPass['npassword'])?$nPass['npassword']:'';
            if ($nPass!='')
                $model->password=Yii::$app->getSecurity()->generatePasswordHash($nPass);

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
            return $this->render('update', [
                'model' => $model,
            ]);
    }

    /**
     * Deletes an existing TblUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TblUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TblUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
