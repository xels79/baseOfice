<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\web\Controller;
/**
 * Description of AjaxController
 *
 * @author Александр
 */
class AjaxController  extends Controller{
    public function init(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }
    public function beforeAction($action){
        if (Yii::$app->user->isGuest){
            throw new ForbiddenHttpException('Недостаточно прав');
        }
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Только AJAX');
        }else{
            return parent::beforeAction($action);
        }
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'getmanagerbyfirm'=>['post']
                ],
                
            ],
        ];
    }
    public function actions(){
        return[
            'getmanagerbyfirm'=>'app\controllers\admin\AjaxAction\GetManagerByFirm',
        ];
    }

}
