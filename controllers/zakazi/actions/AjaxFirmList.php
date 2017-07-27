<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxFirmList
 *
 * @author Александр
 */
namespace app\controllers\zakazi\actions;
use Yii;
use app\models\Zakaz;
class AjaxFirmList extends AjaxAction{
    public function run(){
        $firmId=yii::$app->request->post('firmId',0);
        return ['status'=>'ok','items'=>Zakaz::getFirmsListNew($firmId)];
    }
}
