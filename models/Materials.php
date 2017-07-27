<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "materials_opt".
 *
 * @property integer $id
 * @property string $name
 * @property string $options
 * @property string $rem
 */
class Materials extends \yii\db\ActiveRecord
{
    private $tmpOpt=false;
    public static $tblName=false;
    public $rem='';
    private $rul=false;
    private $attrL=false;
    private static $listVal=false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        if (!self::$tblName){
            if (!self::$tblName=\yii::$app->request->get('tblName',false))
                    self::$tblName=\yii::$app->request->post('tblName',false);
        }
        return self::$tblName;
    }
    private static function loadListVal(){
        if ($matOpt=  MaterialsOpt::find()->where(['name'=>self::tableName()])->one()){
            self::$listVal=$matOpt->getOptions();
            $rem=$matOpt->rem;
        }
        if ($tmpSupl=Firms::find()->where(['productsTypes'=>self::tableName()])->select(['id','name'])->asArray()->all()){
            self::$listVal['supplier']=ArrayHelper::map($tmpSupl,'id','name');
        }else{
            self::$listVal['supplier']=[];
        }        
    }
    private function loadOpt(){
        $this->rul=[];
        $this->attrL=[];
        //$keyN=$this->tableSchema->sequenceName;
        $tmp=[];
        
        foreach($this->getTableSchema($this->tableName())->columns as $col){
            if (!in_array($col->name,$this->getTableSchema($this->tableName())->primaryKey)){
                if (!$col->defaultValue)
                    $tmp['req'][]=$col->name;
                $tmp['type'][$col->phpType][]=$col->name;
                $this->attrL[$col->name]=$col->comment;
            }
        }
        $this->rul[]=[$tmp['req'],'required'];
        foreach(array_keys($tmp['type']) as $tKey){
            $this->rul[]=[$tmp['type'][$tKey],$tKey];
        }
        self::loadListVal();
//        echo '<div class="row">';
//        echo \yii\helpers\VarDumper::dumpAsString($this->rul,10,true).'<br>';
//        echo \yii\helpers\VarDumper::dumpAsString($this->attrL,10,true).'<br>';
//        echo \yii\helpers\VarDumper::dumpAsString(self::$listVal,10,true).'<br>';
//        echo '</div>';
        //Yii::$app->end();
        
    }
    public function getSuppliers(){ //Список всех поставщиков для всех материалов
        $rVal=[];
        self::loadListVal();
        if (isset(self::$listVal['supplier'])){
            $tmp=ArrayHelper::map(Firms::find()->where(['type'=>3])->select(['id','name'])->asArray()->all(),'id','name');
            $rVal=array_diff_assoc($tmp, self::$listVal['supplier']);
            //echo \yii\helpers\VarDumper::dumpAsString($rVal,10,true).'<br>';
        }
        return $rVal;
    }
    public static function supplierBase(){
        return ArrayHelper::map(Firms::find()->where(['type'=>3])->select(['id','name'])->asArray()->all(),'id','name');
    }
    public function getListVal(){
        self::loadListVal();
        return $this->listVal;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        if (!$this->rul){
            $this->loadOpt();
        }
        return $this->rul;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        if (!$this->attrL) $this->loadOpt ();
        return $this->attrL;
    }
    public static function getAllMaterialsForList(){
        $tmp=MaterialsOpt::find()->asArray()->all();
        $rVal=[
            'clear'=>[
                'label'=>'Нет',
                'url'=>'#'
            ]
        ];
        $rVal=[];
        foreach ($tmp as $it){
            $rVal[$it['name']]=[
                'label'=>$it['rem'],
                'url'=>['zakazi/material','tblName'=>$it['name']]
            ];
        }
        return $rVal;
    }
    public function getGridViewColumns(){
        $rVal=[];
        $rVal[]=['class' => 'yii\grid\SerialColumn'];
        foreach(array_keys($this->attrL) as $cName){
            $rVal[]=$cName.':ntext';
        }
        $rVal[]=['class' => 'app\widgets\MActionColumn',
                'modelKeyToConfirm'=>'name',
                'confirm'=>'Удалить фирму "{info}" ?'
        ];
        return $rVal;
    }
}
