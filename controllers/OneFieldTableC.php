<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
/**
 * Description of OneFieldTableC
 *
 * @author Александер
 */
class OneFieldTableC extends ControllerMain{
    public $add=[
        'vName'=>'',
        'backName'=>''
    ];
    public $detali=[
        'vName'=>'',
        'backName'=>''
    ];
    public $list=[
        'vName'=>'',
        'backName'=>''
    ];
    public $change=[
        'vName'=>'',
        'backName'=>''
    ];
    protected $clName='';
    public $defaultAction='list';
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
        \yii\helpers\Url::remember();
        return $rVal;
    }

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

    /**
     * Lists all MethodOfExecution models.
     * @return mixed
     */
    public function actionList()
    {
        $model=new $this->clName;
        $dataProvider = new ActiveDataProvider([
            'query' => $model->find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'vName'=>$this->list['vName']
        ]);
    }

    /**
     * Displays a single MethodOfExecution model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetails($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
                'vName'=>$this->detali['vName'],
                'backName'=>$this->detali['backName']
        ]);
    }

    /**
     * Creates a new MethodOfExecution model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new $this->clName;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['list', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'vName'=>$this->add['vName'],
                'backName'=>$this->add['backName']
            ]);
        }
    }

    /**
     * Updates an existing MethodOfExecution model.
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
                'vName'=>$this->change['vName'],
                'backName'=>$this->change['backName']
            ]);
        }
    }

    /**
     * Deletes an existing MethodOfExecution model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['list']);
    }

    /**
     * Finds the MethodOfExecution model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MethodOfExecution the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $md=new $this->clName;
        if (($model = $md->findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Страничка не найдена.');
        }
    }

}
