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
class DesignerRemFile extends RemFile{
    public $errorText='';
    public $model=null;
    protected function bildPath($id,&$fName){
        return \Yii::getAlias('@file').'/'.Zakaz::createZakazFolderName($id).'/designer/'.$fName;
    }
}
