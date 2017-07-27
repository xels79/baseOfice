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
use app\models\Manager;

class AjaxAddManager extends AjaxAction{
    public function run(){
        $model=new Manager();
        if ($tmp['Manager']=Yii::$app->request->post('Manager')){
            if (isset($tmp['Manager']['fone']))
                if ($tmp['Manager']['fone'])
                    $tmp['Manager']['fone']='{"Основной":"'.$tmp['Manager']['fone'].'"}';
                else
                    $tmp['Manager']['fone']='{}';
            else
                $tmp['Manager']['fone']='{}';
            $tmpArr=mb_split(' ',$tmp['Manager']['name']);
            
            $cnt=count($tmpArr);
            if ($cnt>2){
                $tmp['Manager']['surname']=$tmpArr[2];
                $tmp['Manager']['name']=$tmpArr[0];
                $tmp['Manager']['middle_name']=$tmpArr[1];
            }elseif($cnt>1){
                $tmp['Manager']['name']=$tmpArr[0];
                $tmp['Manager']['surname']=$tmpArr[1];
            }else{
                $tmp['Manager']['name']=$tmpArr[0];
            }
            if ($model->load($tmp)){
                //$model->productsTypes='';
                return [
                    'status'=>$model->save()?'ok':'error',
                    'errors'=>$model->errors,
                    'tmp'=>$tmp,'tmpArr'=>$tmpArr
                ];
            }else{
                return ['status'=>'error','errorText'=>'Модель не загрузилась!','tmp'=>$tmp];
            }
        }else{
            return ['status'=>'error','errorText'=>'Незадан массив Manager'];
        }
    }
}
