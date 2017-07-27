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
class AjaxDesignerUpload extends AjaxUpload{
    protected function upload(){
        return $this->model->designerUpload();
    }
}
