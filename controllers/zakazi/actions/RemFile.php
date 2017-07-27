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
use app\models\Zakaz;
class RemFile extends Action{
    public $errorText='';
    public $model=null;
    public function init(){
        parent::init();
        if (\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
    }
    protected function removFile($fullPath,&$fName){
        if (file_exists($fullPath)){
            if (unlink($fullPath))
                return ['status'=>'ok' ,'messText'=>'Файл удалён','fName'=>$fName];
            else
                return ['status'=>'error' ,'messText'=>'Ошибка удаления','fName'=>$fName];
        }else{
            return ['status'=>'error','messText'=>'Файл не найден','fName'=>$fName];
        }
    }
    protected function bildPath($id,&$fName){
        return realpath(\Yii::getAlias('@file').'/'.Zakaz::createZakazFolderName($id).'/zakaz/'.$fName);
    }
    public function run($id,$fName){
        return $this->removFile($this->bildPath($id, $fName),$fName);
    }
}
