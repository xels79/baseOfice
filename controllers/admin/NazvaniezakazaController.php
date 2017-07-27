<?php

namespace app\controllers\admin;

use Yii;
use app\models\OrdersNames;
use yii\data\ActiveDataProvider;
use app\controllers\OneFieldTableC;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MetodispolnenyaController implements the CRUD actions for MethodOfExecution model.
 */
class NazvaniezakazaController extends OneFieldTableC
{
    public function init(){
        parent::init();
        $this->clName='app\models\OrdersNames';
        $this->viewPath='@app/views/admin/metodispolnenya';
        $this->add['vName']='Добавить тип заказа';
        $this->add['backName']='Типы заказов';
        $this->detali['vName']='Тип заказа';
        $this->detali['backName']='Типы заказов';
        $this->list['vName']='Названия типов заказа';
        $this->list['backName']='Типы заказов';
        $this->change['vName']='Изменить название типа заказа';
        $this->change['backName']='Типы заказов';
    }
}
