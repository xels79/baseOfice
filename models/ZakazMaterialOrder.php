<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use app\models\MethodOfExecution;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Description of ZakazMaterialOrder
 *
 * @author Александр
 */
class ZakazMaterialOrder extends Zakaz{
    protected function compareMaterial($val){
        $rVal=[];
        //\yii\helpers\VarDumper::dump(\yii::$app->db->schema->tableNames,10,true);Yii::$app->end();
        if (is_array($val))
            
            foreach ($val as $key=>$el){
                if ($key){
                    if (in_array($key,\yii::$app->db->schema->tableNames)){
                        $tbl=  MaterialsNew::crateObject($key)->findOne($el);
                        $rVal[$key]=$tbl->name;
                    }elseif($key==='supplier'){
                        if ($firm=Firms::findOne($el)){
                            $rVal[$key]=$firm->name;
                        }else{
                            $rVal[$key]='Не найден';
                        }
                    }else{
                        $rVal[$key]=$el;
                    }
                }else{
                    echo 'id: '.$this->id.'<br>';
                    \yii\helpers\VarDumper::dump($val,10,true);//Yii::$app->end();
                }
            }
        return $rVal;
    }
    protected function n_sp(&$val,$del=', '){
        if (mb_strlen($val)) $val.=$del;
    }
    public function getMaterialCount(){
        $rVal=0;
        $mater=$this->getMDetails();
        if (isset($mater['value']))
            $rVal=count($mater['value']);
        return $rVal;
    }
//    public function getDetaliMaterialNameSvodka(){
//        $rVal='Нет';
//        $mater=$this->getMDetails();
//        if ($materName=  ArrayHelper::getValue($mater, 0,false)){
//            if (isset($mater['value'])){
//                unset ($mater['value'][0]);
//                $rVal='';
//                foreach ($mater['value'] as $el){
//                    $this->n_sp($rVal,'; ');
//                    $inf=$this->compareMaterial($el);
//                    $rVal.=isset($inf['supplier'])?'пост.:'.Html::tag('i',$inf['supplier']):'';
//                    $rVal.=isset($inf['paperName'])?$this->n_sp($rVal).'назв.: '.Html::tag('i',$inf['paperName']):'';
//                    $rVal.=isset($inf['colors'])?$this->n_sp($rVal).'цв.: '.Html::tag('i',$inf['colors']):'';
//                    $rVal.=isset($inf['pcolors'])?$this->n_sp($rVal).'цв.: '.Html::tag('i',$inf['colors']):'';
//                    $rVal.=isset($inf['sizes'])?$this->n_sp($rVal).'р-р: '.Html::tag('i',$inf['sizes']):'';
//                    $rVal.=isset($inf['thickness'])?$this->n_sp($rVal).'толщ.: '.Html::tag('i',$inf['thickness']):'';
//                    $rVal.=isset($inf['density'])?$this->n_sp($rVal).'плотн.: '.Html::tag('i',$inf['density']):'';
//                    $rVal.=isset($inf['typeof'])?$this->n_sp($rVal).'тип: '.Html::tag('i',$inf['typeof']):'';
//                    $rVal.=isset($inf['description'])?$this->n_sp($rVal).'опис.: '.Html::tag('i',$inf['description']):'';
//                }
//            }
//            return Html::tag('b',Materials::getAllMaterialsForList()[$materName]['label'].': ').$rVal;
//        }
//        return $rVal;
//    }
    public function getDetaliMaterialNameSvodka2(){
        $rVal=[
            'value'=>[],
            'materialName'=>null,
            'materialKey'=>null,
            'materTypeName'=>''
        ];
        $mater=$this->getMDetails();
        if ($materName=  ArrayHelper::getValue($mater, 0,false)){
            if (isset($mater['value'])){
                $rVal['materTypeName']=Materials::getAllMaterialsForList()[$materName]['label'];
                unset ($mater['value'][0]);
                $rVal['materialName']=$materName;
                $rVal['materialKey']=$mater[0];
                $rVal['value']=[];
                foreach ($mater['value'] as $el){
                        $rVal['value'][]= $this->compareMaterial($el);
                }
            }
            return $rVal;
        }
        return $rVal;
    }

}
