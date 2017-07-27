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
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
/**
 * Description of Zakaz
 *
 * @author Александер
 */
class Zakaz extends _ZakazFile{
//    public $ourWork='';

    public function init(){
        parent::init();
        
        if ($this->isNewRecord){
            $this->dateOfAdmission=date('d.M.Y');
            $this->payment=0;
            $this->deadline=null;
            $this->materialDetails=null;
            $this->parameters=null;
            $this->shipping=null;
        }        
        $this->on(self::EVENT_AFTER_FIND,function($e){
            if ($e->sender->is_material_ordered){
                $e->sender->isMOrdered=true;
                $e->sender->isMOrderedTitle='Материал заказан: '.\yii::$app->formatter->asDate($this->is_material_ordered);            
            }
        });
        
    }
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['dateOfAdmission','deadline','numberOfCopies','accountNumber','is_material_ordered','special_attention','contractors','name','orderTypeTxt','stageTxt'],'string'],
            [['customerManager','orderType','methodOfExecution','payment','stage'],'integer'],
            [['materialDetails','parameters','execCoast','contractors','ourWork','shipping'],'safe'],
            [['totalCost'],'double'],
            [['all_material_recived'],'boolean'],
            [['payment','paymentMethod'],'integer'],
            [['dateOfAdmission'], 'required','message'=>'Должна быть указана дата.'],
            [['numberOfCopies','name','totalCost'],'required','message'=>'Нужно указать.'],
            [['orderType','methodOfExecution','stage','paymentMethod'], 'required','message'=>'Нужно что-нибудь выбрать.'],
            [['customerManager'], 'required','message'=>'Менеджер не выбран.'],
        ]);
    }
//    public function setCustomerName($val){ //Заказчик
//        
//    }
    public function attributeLabels()
    {
        //return $this->aL();
        return [
            'id'=>'№',
//            'number'=>'№',
            'dateOfAdmission'=>'Дата приёма',
            'dateOfAdmissionFormated'=>'Дата приёма',
            'numberOfCopies'=>'Тираж',
            'customerName'=>'Заказчик',
            'customerManager'=>'Конт.Лицо',
            'orderType'=>'Продукция',
            'orderTypeTxt'=>'Продукция',
            'searchOrderType'=>'Продукция',
            'oManager'=>'Менеджер',
            'searchManagerId'=>'Менеджер',
            'name'=>'Наименование',
            'deadline'=>'Срок сдачи',
            'searchDeadline'=>'Срок сдачи',
            'contractors'=>'Выбрать исполнителя',
            'methodOfExecution'=>'Способ печати',
            'stage'=>'Этапы работы',
            'stageTxt'=>'Этапы работы',
            'searchStageId'=>'Этапы работы',
            'payment'=>'Оплата',
            'paymentTxt'=>'Оплата',
            'totalCost'=>'Общая стоимость заказа:',
            'paymentMethod'=>'Способ оплаты',
            'accountNumber'=>'№ Счета',
            'formatProduct'=>'Формат готового изделия',
            'detaliSpecification'=>'Спецификация',
            'special_attention'=>'Особое внимание',
            'isMOrdered'=>'*',
            'hasfile'=>'',
            'searchWorkType'=>'Работа'
        ];
    }
    public static function tableName(){
        return 'zakaz';
    }
    public static function createCustomerListForFilter(){
        return ArrayHelper::map(Firms::find()->orderBy(['name' => SORT_ASC])->andFilterWhere(['like', 'firmtype','0'])->select(['id','name'])->asArray()->all(),'id','name');
    }
    public static function getExecuterList(){
        return self::getFirmsListNew(1);
    }
    public function firmNameById($fId){
        if ($fId)
            if ($tmp=Firms::find()->where(['id'=>$fId])->one())
                return $tmp->name;
            else
                return '';
        else
            return '';
    }
    public function pantonFace($num){
        if (!$this->_pantonFace) $this->_pantonFace=ArrayHelper::getValue($this->param,'pantonFace',false);
        return ArrayHelper::getValue($this->_pantonFace,$num,null);
    }
    public function pantonBack($num){
        if (!$this->_pantonBack) $this->_pantonBack=ArrayHelper::getValue($this->param,'pantonBack',false);
        return ArrayHelper::getValue($this->_pantonBack,$num,null);        
    }
    public function beforeValidate(){
        $errMess='Неверное значение (допуск. число * число)';
        if (in_array('numberOfCopies', $this->activeAttributes())){
            if (!$this->numberOfCopies){
                $this->addError('numberOfCopies','Должно быть запонено');
                return false;
            }
            $tmp=mb_split('[xх*XХ]', $this->numberOfCopies);
            if (count($tmp)>1){
                if (count($tmp)>2){
                    $this->addError('numberOfCopies',$errMess);
                    return false;
                }elseif(!is_numeric($tmp[0])||!is_numeric($tmp[1])){
                    $this->addError('numberOfCopies',$errMess);
                    return false;
                }else{
                    $tmp=[(int)$tmp[0],(int)$tmp[1]];
                    if ($tmp[0]<=$tmp[1]){
                        $this->numberOfCopies=$tmp[0].'*'.$tmp[1];
                    }else{
                        $this->numberOfCopies=$tmp[1].'*'.$tmp[0];
                    }
                    return true;
                }
            }else{
                if (!is_numeric($this->numberOfCopies)){
                    $this->addError('numberOfCopies',$errMess);
                    return false;
                }
            }
        }
        return true;
    }
    public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
            //$errMess='Неверное значение (допуск. число * число)';
            if ($this->saveUpdateTime) $this->lastChange= time();
            if ($this->is_material_ordered!==null)
                $this->is_material_ordered=\yii::$app->formatter->asDate($this->is_material_ordered,'php:Y-m-d');
            if ($this->runBeforSave){
                $this->processPayment();
                //\yii\helpers\VarDumper::dump($this->getShippingOplata(),10,true);Yii::$app->end();
                $this->prepareToSaveMaterialDetails();
                $this->contractors=Json::encode($this->execCoast);
                $this->processParameters();
//                $this->ourWork='{}';
                $this->dateOfAdmission=\yii::$app->formatter->asDate($this->dateOfAdmission,'php:Y-m-d');
                if ($this->deadline) $this->deadline=\yii::$app->formatter->asDate($this->deadline,'php:Y-m-d');
                $this->shipping=Json::encode($this->shipping);
            }
//            if (is_array($this->_oplata)) $this->oplata= Json::encode ($this->_oplata);
            return true;
        }else{
            return false;
        }

    }
    private function processParameters(){
        $tmp=[];
        if (is_array($this->parameters)){
            foreach($this->parameters as $key=>$val){
                if ($key!=='post'){
                    $tmp[$key]=$val;
                }else{
                    $tmpP=[];
                    foreach ($val as $keyP=>$valP){
                        if ($valP!==''&&$valP!==null){
                            $tmpP[$keyP]=$valP;
                        }
                    }
                    $tmp[$key]=$tmpP;
                }
            }
            $this->parameters=Json::encode($tmp);
        }
    }
    public static function oManagerList(){
        return ArrayHelper::map(TblUser::find()->orderBy(['realname' => SORT_ASC])->select(['id','realname'])->asArray()->all(),'id','realname');
    }
//    public function setParameters($val){
//        $this->parameters='test';
//    }
    public static function GDRenderSearchFirm($model, $key, $index, $column){
        $man=$model->manager;
        $fone='';
        if ($man->foneCount){
            //if ()
        }
        return yii\helpers\Html::tag('span',$model->currentCustomerFirmName,[
            'class'=>'listGVF',
            'title'=>'Контактное лицо: '.$model->manager->name
        ]);
    }
    public static function GDRenderStage($model, $key, $index, $column){
        if (Yii::$app->user->identity->role==='admin' || $model->managerId===Yii::$app->user->identity->id){
            return Html::dropDownList('stage',$model->stage,self::_stages,[
                    'role'=>'lInteractive',
                    'back'=>$model->stage,
                    'class'=>'form-control',
                    'disabled'=>true
                ]);
        }else{
            return $model->stageTxt;
        }
    }
    public static function GDRowSetting ($model, $key, $index, $grid){
        return [];//['manName'=>$this->getOManager()];
    }
    public static function GDRenderPayment($model, $key, $index, $column){
        return $model->getPaymentStage();
//        if (Yii::$app->user->identity->role==='admin' || $model->managerId===Yii::$app->user->identity->id){
//            return Html::dropDownList('payment',$model->payment,self::_payments,[
//                'role'=>'lInteractive',
//                'back'=>$model->payment,
//                'class'=>'form-control',
//                'disabled'=>true
//            ]);
//         }else{
//             return $model->paymentTxt;
//         }
    }
    public function GDStageContentOptions(){
        $rVal=[];
        if ($this->stage<3){
            $rVal['class']='cRed';
        }elseif($this->stage==3){
            $rVal['class']='cGreen';
        }

        return $rVal;
    }
    public function GDRenderPaymentContentOptions(){
        $rVal=[];
        switch ($this->processPayment(false)){
            case (0):
                $rVal['class']='cRed';
                break;
            case (1):
                $rVal['class']='cYellow';
                break;
            case (2):
                $rVal['class']='cGreen';
                break;
        }
        return $rVal;
    }
    public static function createSF($attr,$format='text',$val=false,$filter=false,$label=null,$options=null,$footer=null){
        $rVal= [
            'attribute'=>$attr,
            'format'=>$format,
            'filter'=>$filter
        ];
        if ($footer!==null) $rVal['footer']=$footer;
//        if (is_array($filter)){
//            $rVal['filterOptions']=['class'=>'disabled'];
//        }
        if ($label!==null) $rVal['label']=$label;
        if ($val) $rVal['value']=$val;
        if ($options) $rVal['contentOptions']=$options;
        switch ($attr){
            case 'numberOfCopies':
                $rVal['contentOptions']=function ($model){
                    return ['title'=>'Всего: '.eval("return $model->numberOfCopies;").'шт.'];
                };
                break;
            case 'searchFirm':
                $rVal['content']=self::className().'::GDRenderSearchFirm';
                break;
            case 'searchStageId':
                   $rVal['content']=self::className().'::GDRenderStage';
                   $rVal['contentOptions']=function ($model, $key, $index, $column){
                       return $model->GDStageContentOptions();
                   };
                break;
            case 'searchPayment':
//                   $rVal['content']=self::className().'::GDRenderPayment';
                   $rVal['contentOptions']=function ($model, $key, $index, $column){
                       return $model->GDRenderPaymentContentOptions();
                   };
                break;
            case 'searchDeadline':
                $rVal['content']=function ($model, $key, $index, $column){
                    if ($model->deadline!=='1970-01-01'&&$model->deadline)
                        return \yii::$app->formatter->asDate($model->deadline);
                    else
                        return 'Не задано';
                };
                break;
        }
        return $rVal;
    }
    private function checkInSlaveTbl($tNameDirt,$val,$toproduct){
        $tName='app\\models\\'.strtoupper(substr($tNameDirt, 0,1)).  substr($tNameDirt, 1,  strlen($tNameDirt)-1);
        $model=new $tName();
        if (is_numeric($val)){
            $tmp=$model->findOne((int)$val);            
            if ($tmp){
                if (mb_strpos($tmp->toproduct,$toproduct)===false){
                    $tmp->toproduct.=';'.$toproduct;
                    $tmp->update();
                }
                return $tmp->id;
            }
        }else{
            if ($val[0]=='@'){
                $val= mb_substr($val, 1,mb_strlen($val)-1);
            }
            if ($tmp=$model->find()->where(['name'=>$val])->one()){
                if (mb_strpos($tmp->toproduct,$toproduct)===false){
                    $tmp->toproduct.=';'.$toproduct;
                    $tmp->update();
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
    private function pTSMDProcessField(&$el,$tblName,&$matCnt){
        $model=MaterialsNew::crateObject($tblName);
        $toSave=[];
        foreach ($el as $key=>$val){
            if($key!=='field'){
                if ($key==='dateOfGet'){
                    if ($val=='') $val=null;
                    if ($val) $matCnt--;
                }
                if ($key==='dateOfOrder'&&$val=='') $val=null;
                $toSave[$key]=$val;
            }
            
        }
//        $toSave=[
//            'supplierType'=>$el['supplierType'],
//            'count'=>$el['count'],
//            'summ'=>$el['summ'],
//            'priceppc'=>isset($el['priceppc'])?$el['priceppc']:0
//        ];
        $tmp=[];
        foreach($el['field'] as $fldN=>$val){
            if ($fldN!='supplier'){
                $val=  mb_strtolower ($val);
//                \yii\helpers\VarDumper::dump($el['field'],10,true);echo '<br>';
//                if ($tblName!='pcolors')
//                    $tmp[$fldN]=$this->checkInSlaveTbl($fldN, $val, $tblName);
//                else{
                    if ($fldN=='pcolors'&&array_key_exists('paperName', $el['field'])&&array_key_exists('paperName', $tmp)){
                        $pname= PaperName::findName($tmp['paperName']);
                        if (!$pname) $pname='paper';
                        $tmp[$fldN]=$this->checkInSlaveTbl($fldN, $val, $pname);
//                        \yii\helpers\VarDumper::dump($pname,10,true);echo '<br>';
//                        \yii\helpers\VarDumper::dump($tmp,10,true);echo '<br>';
                    }else
                        $tmp[$fldN]=$this->checkInSlaveTbl($fldN, $val, $tblName);
//                }
            }else{
                $tmp[$fldN]=(int)$el['field']['supplier'];
            }
        }
        $tmp2=$model->findByParam($tmp);
        if ($tmp2){
            $toSave['materialsIdList']=$tmp2->id;
        }else{
            $model->setAttributes($tmp,false);
            /*********РАСКОМЕНИРУЙ ДЛЯ СОХРАНЕНИЯ*********/
            if ($model->save()){
                $toSave['materialsIdList']=$model->id;
            }else{
                $toSave['materialsIdList']=$model->errors;
            }
        }
//        \yii\helpers\VarDumper::dump($el,10,true);
//        \yii\helpers\VarDumper::dump($toSave,10,true);
//        Yii::$app->end();
        return $toSave;
    }
    public function prepareToSaveMaterialDetails(){
//        \yii\helpers\VarDumper::dump($_POST,10,true);Yii::$app->end();
        if (is_string($this->materialDetails))return Json::decode ($this->materialDetails);
        $tmpMDet=$this->materialDetails;
        if (!$this->materialDetails[0]){
            $this->materialDetails='{"necessarySumm":0,"totalSumm":0,"materialsIdList":0}';
        }else{
            $toSave=[
                0=>$this->materialDetails[0],
                'necessarySumm'=>$this->materialDetails['necessarySumm'],
                'totalSumm'=>$this->materialDetails['totalSumm'],
            ];
            if (isset($tmpMDet['value']['productsize'])) $toSave['productsize']=$tmpMDet['value']['productsize'];
            if ($this->materialDetails[0]!=='paper'){
                $matCnt=1;
                $toSave['value']=$this->pTSMDProcessField($tmpMDet['value'], $this->materialDetails[0],$matCnt);
            }elseif(isset($tmpMDet['value'])){
                $toSave['value']=[];
                $matCnt=count($tmpMDet['value']);
                foreach($tmpMDet['value'] as $el){
                    $tmpTS=$this->pTSMDProcessField($el, $this->materialDetails[0],$matCnt);
                    $toSave['value'][]=$tmpTS;
                }
            }
            if (!$matCnt){
                //echo 'All materials is get '. \yii\helpers\VarDumper::dumpAsString($matCnt);Yii::$app->end();
                $this->all_material_recived=true;
                //Yii::$app->end();
            }else{
                $this->all_material_recived=false;
            }
            
            $this->materialDetails=Json::encode($toSave);
//            \yii\helpers\VarDumper::dump($toSave,10,true);
//            Yii::$app->end();

            return $toSave;
 
        }
    }
    public function detaliExexCost(){
        function it($lbl,$val,$form='text'){
            return [
                'label'=>$lbl,
                'value'=>$val,
                'format'=>$form
            ];
        }
        $firstOpt=['class'=>'col-xs-3 table table-striped table-bordered detail-view'];
        $rVal='';
        $tmp=$this->getExecCoast();
        $i=1;
        if (isset($tmp['value'])){
            $rVal=Html::beginTag('div',['class'=>'row']);
//            echo \yii\helpers\VarDumper::dumpAsString($tmp, 10, true);
            //\yii::$app->end();
            foreach ($tmp['value'] as $el){
                $attr=[];
                if (isset($el['idFirm']))
                    if ($el['idFirm']>0)
                        $attr[]=it('Фирма',$this->firmNameById($el['idFirm']));
                    else
                        $attr[]=it('Фирма','Не указана');
                if (isset($el['idManager']))
                    if ($el['idManager']>0)
                        $attr[]=it('Конт. лицо',$this->getManagerById($el['idManager']));
                $attr[]=it('Вид работы',$this->MetodIspolnenyTextByID($el['methodOfExecution']));
                $attr[]=it('Сумма',$el['payments']);
                if ($i==3){
                    $rVal.=Html::endTag('div').Html::beginTag('div',['class'=>'row']);
                    //Html::addCssClass($firstOpt, 'pull-left');
                }
                
                $rVal.=yii\widgets\DetailView::widget([
                    'model'=>$el,
                    'attributes'=>$attr,
                    'options'=>$firstOpt
                ]);
                $i++;
            }
            $rVal.=Html::endTag('div');
        }
        
        return $rVal;
    }
    public function renderMaterPayed(&$val,$index=0){
        $rVal='';
        if ($val['summ']!=0){
            if ($payed= \yii\helpers\ArrayHelper::getValue($val, 'payed',null)){
                $checked=[
                    'class'=>'glyphicon glyphicon-check',
                    'title'=>Yii::$app->formatter->asDate($payed)
                ];
            }else{
                $checked=['class'=>'glyphicon glyphicon-unchecked','role'=>'checkbexec'];
            }
            $cont=Html::tag('span',Yii::$app->formatter->asDecimal($val['summ'],0));
            $cont.=Html::tag('span',null,$checked);
            $opt=['requestfor'=>'material','elnum'=>$index,'class'=>'td-raw','title'=>'Поставщик: '.$this->firmNameById($val['supplier']),'data-placement'=>'left','info-text'=>'Материал'];//68
            $rVal=Html::tag('div',$cont,$opt);
        }
        return $rVal;
    }
    public function getPrib(){
        $tmp=$this->getExecCoast();
        $summExec=0;
        $summBonus=0;
        if (isset($tmp['value'])){
            if (is_array($tmp['value'])){
                $rVal='';
                foreach($tmp['value'] as $index=>$el){
                    if ($el['idFirm']==68)
                        $summBonus+=(double)$el['payments'];
                    else
                        $summExec+=(double)$el['payments'];
                }
            }
        }
        $tmp=$this->mDetails;
        $summMat=0;
        if ($value= \yii\helpers\ArrayHelper::getValue($tmp,'value'))
            if (is_array($value))
                if ($cnt=count($value)){
                    for ($i=1;$i<$cnt;$i++){
                        $summMat+=(double)$value[$i]['summ'];
                    }
                }
        return [
            'summ'=>(double)$this->totalCost-$summExec-$summMat-$summBonus,
            'summMat'=>$summMat,
            'summExec'=>$summExec,
            'summBonus'=>$summBonus
        ];
    }
    public function copyPrepareSaveMaterial($val){
        $tmp=$this->materialDetails;
        //$str = str_replace("ll", "", "good golly miss molly!", $count);
    }
}