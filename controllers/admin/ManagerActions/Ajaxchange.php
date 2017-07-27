<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\controllers\admin\ManagerActions;
use Yii;
use app\models\Manager;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Description of Ajaxchange
 *
 * @author Александер
 */
class Ajaxchange extends AjaxEdit{
    public function run(){
        $id=yii::$app->request->post('id',false);
        if (!$id)$id=yii::$app->request->get('id',false);
        if ($id){
            $this->model=$this->findModel($id);
            return parent::run();
        }else{
            throw new InvalidConfigException('Не задан id модели!');
        }
    }
    protected function findModel($id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запись #'.\yii\helpers\VarDumper::dump($id).' не найдена.');
        }
    }

}
