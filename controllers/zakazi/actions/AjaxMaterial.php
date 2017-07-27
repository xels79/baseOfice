<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\zakazi\actions;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\MaterialsNew;
use yii\helpers\Html;
//use app\models\Zakaz;
class AjaxMaterial extends AjaxAction{
    public $tblName=false;
    private $inf=[];
    private $supplier=false;
    private $selectedSupplier=false;
    private $paperNum=false;
    private $isSecond=false;
    public $recId=false;
    public $values=false;
    public function init(){
        parent::init();
        if (!$this->isSecond) $this->isSecond=Yii::$app->request->post('isSecond',false);
        if (!$this->tblName) $this->tblName=Yii::$app->request->post('tblName',false);
        if (!$this->selectedSupplier) $this->selectedSupplier=Yii::$app->request->post('selectedSupplier',false);
        if (!$this->recId) $this->recId=Yii::$app->request->post('recId',false);
        if (!$this->paperNum) $this->paperNum=Yii::$app->request->post('paperNum',1);
        if (!$this->values) $this->values=Yii::$app->request->post('values',[]);
        $this->inf['tblName']=$this->tblName;
        $this->inf['selectedSupplier']=$this->selectedSupplier;
        $this->inf['values']=$this->values;
        $this->supplier=MaterialsNew::supplierBase();
    }
    private function initModel(){
        if ($this->recId===false){
            return new MaterialsNew();
        }else{
            return MaterialsNew::findOne(['id'=>$this->recId]);
        }        
    }
    private function base(){
        return [
            'supplier'=>$this->supplier,
            'model'=>$this->initModel(),
            'values'=>$this->values
        ];
    }
    private function paper(){
        return array_merge([
            'selectedSupplier'=>$this->selectedSupplier,
            'paperNum'=>$this->paperNum,
            'isSecond'=>$this->isSecond            
        ],$this->base());
    }
    private function standart(){
        return array_merge([
            'cenLabel'=>'Цена шт:',
            'countName'=>'Количество:'
        ],$this->base());
    }
    private function plastic(){
        return array_merge([
            'cenLabel'=>'Цена за лист:',
            'countName'=>'Тираж (с запасом):',
            //'dopField'=>['productsize'=>['label'=>'Размер изделия']]
        ],$this->base());
    }
    private function selfAdhesivePaper(){
        return array_merge([
            'cenLabel'=>'Цена за лист:',
            'countName'=>'Тираж (с запасом):',
            'dopField'=>[
                //'productsize'=>['label'=>'Размер изделия'],
                'number_of_sheets'=>['label'=>'Листов на тираж'],
                ]
        ],$this->base());
    }
    private function selfAdhesivePolypropylene(){
        return array_merge([
            'cenLabel'=>'Цена за лист:',
            'countName'=>'Тираж (с запасом):',
            'cenLabelM2'=>'Цена за кв.м.:',
            'dopField'=>[
                //'productsize'=>['label'=>'Размер изделия'],
                'number_of_sheets'=>['label'=>'Листов на тираж','default'=>0],
                'number_of_meter'=>['label'=>'Количество погонных метров','default'=>0]
                ]
        ],$this->base());
    }
    public function run(){
        $rVal['status']='ok';
        switch ($this->tblName){
            case 'paper':                   //Бумага
                $rVal['html']=$this->controller->renderPartial('mater/paper.php',$this->paper());
                break;
            case 'packages':                //Пакеты
                $rVal['html']=$this->controller->renderPartial('mater/other.php',$this->standart());
                break;
            case 'foldersСorners':          //Папки уголки
                $rVal['html']=$this->controller->renderPartial('mater/other.php',$this->standart());
                break;
            case 'fajliki':                 //Файлики
                $rVal['html']=$this->controller->renderPartial('mater/other.php',$this->standart());
                break;
            case 'plastic':                 //Пластик
                $rVal['html']=$this->controller->renderPartial('mater/other.php',$this->plastic());
                break;
            case 'selfAdhesivePaper':       //Самоклейкая бумага
                $rVal['html']=$this->controller->renderPartial('mater/other.php',$this->selfAdhesivePaper());
                break;
            case 'selfAdhesivePolypropylene'://Самоклейкая плёнка
                $rVal['html']=$this->controller->renderPartial('mater/selfAdhesive.php',$this->selfAdhesivePolypropylene());
                break;
            //plastic
            default:
                $rVal['html']=Html::tag('div','Не обрабатывается',['id'=>'sMaterial']);
                break;
        }
        $rVal['info']=$this->inf;
        return $rVal;
    }
}
