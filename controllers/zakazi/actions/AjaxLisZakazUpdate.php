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
class AjaxLisZakazUpdate  extends AjaxAction{
    public $idZ;
    public $attr;
    public function init(){
        parent::init();
        $this->idZ=yii::$app->request->post('id');
        $this->attr=yii::$app->request->post('attrToSave',[]);
    }
    protected function updateOtherParam(&$model,&$rVal){
        if ($model->load(Yii::$app->request->post())){
            $model->runBeforSave=false;
            $model->saveUpdateTime=false;
            if (!$model->is_material_ordered) $model->is_material_ordered=null;
            $attrToSave=\yii::$app->request->post('attrToSave',[]);
            if ($model->save(true)){
                $rVal['status']='ok';
                $rVal['html']='Заказ №'.$model->id.' успешно обновлён!';
                $rVal['model']=$model->toArray();
                //$rVal['model']=$model->toArray();
            }else{
                $rVal['errorText']='model: ошибка сохранения';
                $rVal['error']=$model->errors;
            }
        }else{
            $rVal['status']='error';
            $rVal['errorText']='Данные не переданы!';
        }
    }
    protected function  materialDateParam(&$model,&$rVal){
        $materialDateParam=Yii::$app->request->post('materialDateParam',false);
        if ($materialDateParam){
            if (isset($materialDateParam['material-index'])&&isset($materialDateParam['varName'])&&$materialDateParam['value']){
                if (!isset($materialDateParam['value'][$materialDateParam['varName']])){
                    $rVal['errorText']='Не передано значение для "'.$materialDateParam['varName'].'"!';
                    return;
                }
                $material=$model->prepareToSaveMaterialDetails();
                if ($materialDateParam['material-index']>count($material['value'])){
                    $rVal['errorText']='Не верный индекс материала';
                    return;                    
                }
                if ($material[0]==='paper')
                    $material['value'][$materialDateParam['material-index']][$materialDateParam['varName']]=$materialDateParam['value'][$materialDateParam['varName']];
                else
                    $material['value'][$materialDateParam['varName']]=$materialDateParam['value'][$materialDateParam['varName']];
                $model->materialDetails=  \yii\helpers\Json::encode($material);
                $recivedAll=false;
                if ($material[0]==='paper'){
                    foreach ($material['value'] as $el){
                        $recivedAll=isset($el['dateOfGet']);
                        if (!$recivedAll)break;
                    }
                }else{
                    $recivedAll=isset($material['value']['dateOfGet']);
                }
                $attrToSave=['materialDetails'];
                //if ($recivedAll){
                    $model->all_material_recived=$recivedAll;
                    $attrToSave[]='all_material_recived';
                    //\yii::trace('Получен','');
                //}
                
                $model->runBeforSave=false;
                $model->saveUpdateTime=false;
                $model->save(true,$attrToSave);
                $rVal['status']='Ok';
                $rVal['material']=$material;
                $rVal['model-materialDetails']=$model->mDetails;
                $rVal['html']='Заказ №'.$model->id.' успешно обновлён!';
            }else
                $rVal['errorText']='Неверные данные!';
        }else{
            $rVal['errorText']='Данные не переданы!';
        }
    }
    public function run(){
        $rVal['status']='error';
        if ($model=Zakaz::findOne($this->idZ)){
            if ($this->controller->checkAccess($model, $this->idZ)){
                if (isset($_POST['Zakaz'])){
                        $this->updateOtherParam($model, $rVal);
                }elseif(isset($_POST['materialDateParam'])){
                        $this->materialDateParam($model, $rVal);
                }else{
                    $rVal['status']='error';
                    $rVal['errorText']='Данные не переданы!';
                }
            }else{
                $rVal['status']='error';
                $rVal['errorText']='Нельзя редактировать чужой заказ';
//                    throw new ForbiddenHttpException('Нельзя редактировать чужой заказ');
            }
        }else{
            $rVal['status']='error';
            $rVal['errorText']='Заказ №'.($this->idZ?$this->idZ:'(не указано)').' не найден!';
        }
        return $rVal;
//    }
//    protected $model=null;
//    public function run(){
//        if ($id=Yii::$app->request->post('id')){
//            if ($id)
//                return ['status'=>'ok','managerList'=>  Zakaz::getManagerList2($id)];
//            else
//                return ['status'=>'ok','managerList'=>  false];
//        }else{
//            return ['status'=>'error','errorText'=>'Незадан id фирмы "$firmId"'];
//        }
//            }else{
//                return ['status'=>'error','errorText'=>'Незадан тип запроса "$rq"'];
//            }
    }

}
