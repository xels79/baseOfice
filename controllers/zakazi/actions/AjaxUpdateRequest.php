<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakazi\actions;
use Yii;
use app\models\Zakaz;
/**
 * Description of AjaxAction
 *
 * @author Александер
 */
class AjaxUpdateRequest extends AjaxAction{
    protected $model=null;
    public function run(){
        if ($firmId=Yii::$app->request->post('firmId')){
            if ($firmId!=1)
                return ['status'=>'ok','managerList'=>  Zakaz::getManagerList2($firmId)];
            else
                return ['status'=>'ok','managerList'=>  false];
        }else{
            return ['status'=>'error','errorText'=>'Незадан id фирмы "$firmId"'];
        }
//            }else{
//                return ['status'=>'error','errorText'=>'Незадан тип запроса "$rq"'];
//            }
    }
}
