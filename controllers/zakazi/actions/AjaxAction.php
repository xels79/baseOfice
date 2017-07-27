<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxAction
 *
 * @author Александр
 */
namespace app\controllers\zakazi\actions;
use Yii;
use yii\base\Action;

class AjaxAction extends Action{
    public $errorText='';
    public function init(){
        parent::init();
        if (\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
    }
}
