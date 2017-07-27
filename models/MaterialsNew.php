<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\widgets\ActiveDropdown;
use yii\helpers\Url;

class MaterialsNew extends \yii\db\ActiveRecord
{
    public static $tblName=false;
    private $rul=false;
    private $attrL=false;
    private $fList=false;
    private $likeUrl;
    /**
     * @inheritdoc
     */
    public function init(){
        parent::init();
        if (!Yii::$app->request->isConsoleRequest)
            $this->likeUrl=Url::to(['ajaxupdaterequest']);
    }
    public function setTblName($val){
        self::$tblName=$val;
    }
    public static function tableName()
    {
        if (!self::$tblName){
            if (!Yii::$app->request->isConsoleRequest)
                if (!self::$tblName=\yii::$app->request->get('tblName',false))
                        self::$tblName=\yii::$app->request->post('tblName',false);
        }
        return self::$tblName;
    }
    public static function crateObject($tName){
        self::$tblName=$tName;
        return new MaterialsNew;
    }
    public function getFieldList(){
        if (!$this->fList)
            $this->loadOpt ();
        
        return $this->fList;
    }
    public function dropDownList($fldN,$pcolor_description=null){
        if (!$this->fList)
            $this->loadOpt ();
        if ($fldN=='pcolors'){
            if (!$pcolor_description){
                $pcolor_description=Yii::$app->request->post('pcolor_description');
            }
            if ($pcolor_description){
                return ArrayHelper::map(Pcolors::find()
                        ->andFilterWhere(['like','toproduct',$pcolor_description])
                        ->select(['id','name'])
                        ->orderBy(['name' => SORT_ASC])
                        ->asArray()
                        ->all(),'id','name');    
            }else
                return [];
        }else{
            if (isset($this->fList[$fldN]['list']))
                return $this->fList[$fldN]['list'];
            else
                return [];
        }
    }
    public function nameByColFDT($fldN){    //Возвращает поле name из таблицы с именем $fldN
                                            //По id из текущей модели из поля с именем $fldN
        $rVal='';
        if (isset($this[$fldN])){
            if (is_numeric($this[$fldN])){
                $tName='app\\models\\'.strtoupper(substr($fldN, 0,1)).  substr($fldN, 1,  strlen($fldN)-1);
                $model=new $tName();
                if ($tmp=$model->findOne(['id'=>$this[$fldN]]))
                    $rVal=$tmp->name;
            }
        }
        return $rVal;
    }
    public function creatField($fName,$nPerf='',$values=[],$afterClick=false){
        if (!$this->fList||!$this->attrL)
            $this->loadOpt ();
        $rVal='';
        if (isset($this->fList[$fName])){
            if (isset($values[$fName])){
                $val=$values[$fName];
            }else{
                $val=$this[$fName]!==null?$this[$fName]:$this->fList[$fName]['default']?$this->fList[$fName]['default']:null;
            }
            if (!isset($this->fList[$fName]['list'])){
                //isset($values['supplierType'])?$values['supplierType']:0
                $rVal=Html::tag(
                        'div',
                        Html::textInput($nPerf.'['.$fName.']',$val,['class'=>'form-control']),
                        ['class'=>'input-group']
                    );
            }else{
                $options=[
                    'selected'=>$val,
                    'id'=>'material-'.$fName,
                    'formControlName'=>$nPerf.'['.$fName.']',
                    'formControlID'=>'materialDetails-'.$fName.'_Mat',
                    'menuId'=>$fName.'_Mat',
                    'items'=>$this->fList[$fName]['list'],
                    'noScript'=>true,
                    'infoTag'=>'input',
                    'placeholder'=>'Выберите...',
                    'likeFilterAjaxUrl'=>$this->likeUrl,
                    'likeRequestName'=>'material',
                    'likeParamAddToLink'=>true,

                ];
                if ($afterClick) $options['afterClickFunction']=$afterClick;
                $rVal=ActiveDropdown::widget($options);
            }
        }
        return $rVal;
    }
    public function createLabel($fName,$opt=[]){
        if (!$this->fList||!$this->attrL)
            $this->loadOpt ();
        $rVal='';
        if (isset($this->fList[$fName])){
            //Html::addCssClass($opt,'control-label');
            $rVal=Html::tag('label',$this->attrL[$fName]?$this->attrL[$fName]:$fName,$opt);
        }else{
            $rVal=Html::tag('label',$fName,$opt);
        }
        return $rVal;
    }
    private function listFromDT($tNameDirt){//получаем списки от зависимой таблиц
        $sVal=null;
        if ($tNameDirt=='pcolor'){
            if (!$this->isNewRecord&&$this->tableName()=='paper'){
                if ($el= PaperName::findOne($this->paperName))
                    $sVal=$el->name;
            }
        }
        $classN='app\\models\\'.strtoupper(substr($tNameDirt, 0,1)).  substr($tNameDirt, 1,  strlen($tNameDirt)-1);
        return ArrayHelper::map($classN::find()
                ->andFilterWhere(['like','toproduct',$sVal!==null?$sVal:$this->tableName()])
                ->select(['id','name'])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all(),'id','name');    
    }
    public static function dTList(){
        return [
            'sizes',
            'colors',
            'pcolors',
            'density',
            'typeof',
            'thickness',
            'paperName',
            'description',
            'type_of_glue',
            'manufacturer',
            'sizes_of_rolls',
            'series_of_tape'
        ];
        
    }
    private function loadOpt(){
        $dTList=self::dTList();
        $this->fList=[];
        $this->rul=[];
        $this->attrL=[];
        //$keyN=$this->tableSchema->sequenceName;
        $tmp=[];
        
        foreach($this->getTableSchema($this->tableName())->columns as $col){
            if (!in_array($col->name,$this->getTableSchema($this->tableName())->primaryKey)){
                $this->fList[$col->name]=[
                    'req'=>!$col->defaultValue?true:false,
                    'default'=>$col->defaultValue?$col->defaultValue:false,
                    'type'=>$col->phpType
                ];
                //получаем списки от зависимых таблиц
                if ($col->name=='supplier'){
                    $this->fList[$col->name]['list']=$this->supplierBase();
                }elseif (in_array($col->name,$dTList)){
                    $this->fList[$col->name]['list']=$this->listFromDT ($col->name);
                }
                if (!$col->defaultValue){
                    $tmp['req'][]=$col->name;
                }
                $tmp['type'][$col->phpType][]=$col->name;
                $this->attrL[$col->name]=$col->comment;
            }
        }
        $this->rul[]=[$tmp['req'],'required'];
        foreach(array_keys($tmp['type']) as $tKey){
            $this->rul[]=[$tmp['type'][$tKey],$tKey];
        }
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
    public function readVFDT(){ //Порочитать все зависимые колонки
                                //и вернуть в масиве [coln=>VAL]
        $rVal=[];
        if (!$this->fList)
            $this->loadOpt ();
        foreach(array_keys($this->fList) as $fName){
            if ($this[$fName]){
                $rVal[$fName]=$this[$fName];
            }else{
                $rVal[$fName]=0;
            }
        }
        return $rVal;
    }
    public static function supplierBase(){
        return ArrayHelper::map(Firms::find()
                ->andFilterWhere(['like','firmtype','2'])
                ->select(['id','name'])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all(),'id','name');
    }
    public function findByParam($param){
        return $this->findOne($param);
    }
    
        private function checkInSlaveTbl($tName,$val,$toproduct){
        $model=new $tName();
        if (is_int($val)){
            if ($tmp=$model->findOne((integer)$val)){
                if (mb_strpos($tmp->toproduct,$toproduct)===false){
                    $tmp->toproduct.=';'.$toproduct;
                    $tmp->save();
                }
                return (integer)$val;
            }
        }else{
            if ($tmp=$model->find()->where(['name'=>$val])->one()){
                if (mb_strpos($tmp->toproduct,$toproduct)===false){
                    $tmp->toproduct.=';'.$toproduct;
                    $tmp->save();
                }
                return $tmp->id;
            }else{
                $model->name=$val;
                $model->toproduct=$toproduct;
                $model->save();
                return $model->id;
            }
        }
    }

}
