<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin\AjaxAction;
use yii\base\Action;
use app\models\Manager;
/**
 * Description of GetManagerByFirm
 *
 * @author Александр
 */
class GetManagerByFirm  extends Action{
    private $error=false;
    private function managers($firmId){
        $rVal=[];
        if ($firmId){
            if ($aq=Manager::find()->where(['firm_id'=>$firmId])->select(['id','name','middle_name','surname','fone'])->asArray()->all()){
                foreach ($aq as $el){
                    $tmp='';
                    $tmp.=$el['name'];
                    if ($el['middle_name'])
                        $tmp.=' '.$el['middle_name'];
                    if ($el['surname']){
                        $tmp.=' '.$el['surname'];
                    }
                    $dop='';
                    if ($el['fone']){
                        $tmp_arr=\yii\helpers\Json::decode($el['fone']);
                        if (count($tmp_arr)) $dop='Тел: '.array_shift($tmp_arr);
                    }
                    $rVal[]=['label'=>$tmp,'value'=>$el['id'],'options'=>['title'=>$dop]];
                }
            }
        }
        return $rVal;
    }
    public function run(){
        $rVal=[
            'list'=>$this->managers(\yii::$app->request->post('id',false))
        ];
        if ($this->error){
            $rVal['status']='error';
            $rVal['errorText']=$this->error;
        }else{
            $rVal['status']='ok';
        }
        return $rVal;
    }
}
