<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxAddFirm
 *
 * @author Александр
 */
namespace app\controllers\zakazi\actions;
use Yii;
use app\models\Firms;

class AjaxAddFirm extends AjaxAction{
    public function run(){
        $model=new Firms();
        if ($tmp['Firms']=Yii::$app->request->post('Firms')){
            if (isset($tmp['Firms']['fone']))
                if ($tmp['Firms']['fone'])
                    $tmp['Firms']['fone']='{"Основной":"'.$tmp['Firms']['fone'].'"}';
                else
                    $tmp['Firms']['fone']='{}';
            else
                $tmp['Firms']['fone']='{}';
            if ($model->load($tmp)){
                $model->productsTypes='';
                //$model->firmtype='["'.$model->type.'"]';
                return [
                    'status'=>$model->save()?'ok':'error',
                    'errors'=>$model->errors,
                    'tmp'=>$tmp
                ];
            }else{
                return ['status'=>'error','errorText'=>'Модель не загрузилась!','tmp'=>$tmp];
            }
        }else{
            return ['status'=>'error','errorText'=>'Незадан массив Firm'];
        }
    }
}
