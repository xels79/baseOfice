<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin\ManagerActions;
use Yii;
use yii\base\Action;
/**
 * Description of AjaxMain
 *
 * @author Александер
 */
class AjaxEdit extends Action{
    protected $model=null;
    public function init(){
        parent::init();
        if (\Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
    }
    public function run(){
        if (\Yii::$app->request->isAjax){
            if ($this->model->load(Yii::$app->request->post())) {
                if (!$vlAttr=Yii::$app->request->post('validateAttributes',false)){
                    return [
                        'status'=>$this->model->save()?'ok':'error',
                        'errors'=>$this->model->errors
                        ];
                }else{
                    $err=false;
                    foreach ($vlAttr as $attrKey){
                        if (!$err)
                            $err=$this->model->validate($attrKey);
                        else
                            $this->model->validate($attrKey);
                    }
                    return [
                        'validateAttributes'=>$vlAttr,
                        'status'=>!$err?'validate':'error',
                        'errors'=>$this->model->errors
                        ];
                }
            } else {
                if (Yii::$app->request->post('firm_id')) 
                    $this->model->firm_id=Yii::$app->request->post('firm_id');
                    $rVal= [
                    'html'=>$this->controller->renderPartial('_form', [
                        'model' => $this->model
                        ]),
                    ];
                $rVal['options']=\app\widgets\ActiveFones::$returnOptions;
                return $rVal;
            }
        }
    }
}
