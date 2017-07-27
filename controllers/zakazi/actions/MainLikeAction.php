<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakazi\actions;
use app\models\Zakaz;
/**
 * Description of AddAction
 *
 * @author Александр
 */
class MainLikeAction extends AjaxAction{
    public function run(){
        $model = new Zakaz();
        $model->managerId=\yii::$app->user->id;
        return ['status'=>'ok','html'=>$this->controller->renderPartial('mainLike',['model'=>$model])];
    }
}
