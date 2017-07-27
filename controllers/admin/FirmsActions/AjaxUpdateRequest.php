<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin\FirmsActions;
use Yii;
use yii\base\Action;
use app\models\Firms;
/**
 * Description of AjaxUpdateRequest
 *
 * @author Александер
 */
 
class AjaxUpdateRequest extends Action{
     public function init(){
        parent::init();
        if (\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
    }
    
    public function run($id=false){
       if (!$id) $id=\Yii::$app->request->post('id');
       if (($model = Firms::findOne($id)) !== null) {
        return [
             'status'=>'ok','html'=>$this->controller->renderPartial('managerListBox',['managers'=>$model->managers,'modelId'=>$id])
        ];
       }else{
           return[
               'status'=>'error','text'=>'id не найден'
           ];
       }
    }
}