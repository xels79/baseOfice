<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakazi\actions;
use Yii;
/**
 * Description of AjaxLisZakazUpdate
 *
 * @author Александр
 */
use app\models\Zakaz;
use yii\web\ForbiddenHttpException;
class AjaxLisZakazBugalterUd  extends AjaxAction{
    public $idZ;
    public function init(){
        parent::init();
        $this->idZ=yii::$app->request->post('id');
    }
    public function run(){
        $rVal['status']='error';
        if ($model=Zakaz::findOne($this->idZ)){
            if($materialDateParam=Yii::$app->request->post('materialDateParam')){
                $rVal['html']='Выполнено. Изменения не сохранены!'; 
                if ($materialDateParam['requestFor']==='executer'){
                    $execCoast=$model->execCoast;
                    if (isset($execCoast['value']))
                        if (is_array($execCoast['value'])){
                            if (isset($execCoast['value'][$materialDateParam['index']])){
                                $execCoast['value'][$materialDateParam['index']]['payed']=$materialDateParam['value']['payed'];
                                $model->contractors= \yii\helpers\Json::encode($execCoast);
                                $model->runBeforSave=false;
                                $model->saveUpdateTime=false;
                                if ($model->save(true,['contractors']))
                                    $rVal['html']='Заказ №'.$this->idZ.' успешно обновлён!';
                                else{
                                    $rVal['errorText']='Ошибка сохранения модели!<br>'.\yii\helpers\VarDumper::dumpAsString($model->errors,10,true);
                                    return $rVal;
                                }
                            }else{
                                $rVal['errorText']='Индекс исполнителя "'.$materialDateParam['index'].'" не найден в заказе';
                                return $rVal;                            
                            }
                        }else{
                            $rVal['errorText']='Ошибка в данных заказа';
                            return $rVal;                            
                        }
                    else{
                        $rVal['errorText']='Нет исполнителей';
                        return $rVal;
                    }
                }elseif($materialDateParam['requestFor']==='material'){
                    $mater=$model->prepareToSaveMaterialDetails();
                    $rVal['html'].=' Материалы';
                    if ($value= \yii\helpers\ArrayHelper::getValue($mater,'value'))
                        if (is_array($value)){
                            if ($mater[0]==='paper'){
                                if (isset($mater['value'][(int)$materialDateParam['index']-1])){
                                    $mater['value'][(int)$materialDateParam['index']-1]['payed']=$materialDateParam['value']['payed'];
                                }else{
                                    $rVal['errorText']='Индекс материала "'.$materialDateParam['index'].'" не найден в заказе';
                                    $rVal['material[value]']=$value;
                                    //$rVal['count']=$cnt;
                                    return $rVal;                            
                                }
                            }else{
                                $mater['value']['payed']=$materialDateParam['value']['payed'];
                            }
                            $model->runBeforSave=false;
                            $model->saveUpdateTime=false;
                            $model->materialDetails=  \yii\helpers\Json::encode($mater);
                            if ($model->save(true,['materialDetails']))
                                $rVal['html']='Заказ №'.$this->idZ.' успешно обновлён!';
                            else{
                                $rVal['errorText']='Ошибка сохранения модели!<br>'.\yii\helpers\VarDumper::dumpAsString($model->errors,10,true);
                                return $rVal;
                            }
                        }
                }
                $rVal['status']='ok';
            }else{
                $rVal['status']='error';
                $rVal['errorText']='Данные не переданы!';
            }
        }else{
            $rVal['status']='error';
            $rVal['errorText']='Заказ №'.($this->idZ?$this->idZ:'(не указано)').' не найден!';
        }
        return $rVal;
    }
}
