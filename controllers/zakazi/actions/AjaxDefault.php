<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakazi\actions;
use app\controllers\zakazi\actions\AjaxAction;
use yii\web\HttpException;
use yii;
/**
 * Description of AjaxDefault 
 *
 * @author Александер
 */
class AjaxDefault extends AjaxAction{

    public function run(){
        if (\Yii::$app->request->isAjax){
            return ['status'=>'error','errorText'=>$this->errorText,'post'=>yii::$app->request->post()];
        }else{
            throw new HttpException('505',$this->errorText);
            return null;
        }
    }
}
