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
use yii\web\UploadedFile;
class AjaxUpload extends Action{
    public $errorText='';
    public $model=null;
    public function init(){
        parent::init();
        if (\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        if ($id=\yii::$app->request->post('id')){
            if ($this->model=\app\models\Zakaz::findOne($id)){
                $this->model->fileInputUpLoad=UploadedFile::getInstanceByName('fileInputUpLoad');
            }
        }
    }
    protected function upload(){
        return $this->model->upload();
    }
    public function run(){
        if ($this->model){
            if (!$tmp=$this->upload()){
                return ['status'=>'ok','post'=>$_POST,'file'=>$_FILES];
            }else{
                return ['status'=>'error','error'=>$tmp,'post'=>$_POST,'file'=>$_FILES];
            }
        }else{
            return ['status'=>'error','errorText'=>'Не задан индекс заказа или заказ не найден','post'=>$_POST,'file'=>$_FILES];
        }
    }
}
