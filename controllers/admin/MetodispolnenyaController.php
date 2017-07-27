<?php

namespace app\controllers\admin;

use Yii;
use app\models\MethodOfExecution;
use yii\data\ActiveDataProvider;
use app\controllers\OneFieldTableC;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MetodispolnenyaController implements the CRUD actions for MethodOfExecution model.
 */
class MetodispolnenyaController extends OneFieldTableC
{
    public function init(){
        parent::init();
        $this->clName='app\models\MethodOfExecution';
        $this->viewPath='@app/views/admin/metodispolnenya';
        $this->add['vName']='Добавить метод исполнения';
        $this->add['backName']='Методы исполнения';
        $this->detali['vName']='метод исполнения';
        $this->detali['backName']='Методы исполнения';
        $this->list['vName']='Методы исполнения';
        //$this->list['backName']='Типы заказов';
        $this->change['vName']='Изменить название метода исполнения';
        $this->change['backName']='Методы исполнения';
    }
}
