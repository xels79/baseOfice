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
class AjaxDownload extends Action{
    public $errorText='';
    public $model=null;
    public function init(){
        parent::init();
    }
    public function run($id,$fName,$isInputFiles=true){
        
        $file=realpath(\Yii::getAlias('@file').'/'. \app\models\Zakaz::createZakazFolderName($id).'/'.($isInputFiles?'zakaz':'designer').'/'.$fName);
        if (file_exists($file)){
            //return \yii\helpers\Html::encode($file);
            return \yii::$app->response->sendFile($file,$fName);
        }else{
            return 'error #file:'.$file;
        }
    }
}
