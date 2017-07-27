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
 * Description of _Zakaz
 *
 * @author Александр
 */
abstract class _Zakaz extends \yii\db\ActiveRecord  {
    //public $id;                 //ID заказа
    /*
    public $number;             //Номер заказа
    public $dateOfAdmission;    //Дата приёма
    public $managerId;          //ID нашего менеджера
    public $customerManager;    //Заказчик
    public $orderType;          //ID Вида заказа
    public $name;               //Наименование
    public $numberOfCopies;     //Тираж
    public $methodOfExecution;  //ID Метода исполнения
    public $contractors;        //Исполнители в виде JSON
                                //{ id(исполнителя):цена }
    public $deadline;           //Срок сдачи
    //public $supplier;           //id Поставщика
    //public $material;           //id названия материала
    public $materialDetails;    //Подробности заказа НЕ ДОЛЖНО СОХРАНЯТЬСЯ В ТАБЛИЦЕ
    public $stage;              //Этапы работы
    public $payment;            //Оплата: Неоплачен,Предоплата,Оплачен
    public $totalCost;          //Общая стоимость заказа
    public $paymentMethod;      //Способ оплаты
    public $accountNumber=false;//Номер счетаs
    public $handed=false;       //сдан дата или false
     * 
     */
    //public $accountNumber='Не выставлено';
    protected $execCoast=[];
    protected $_parameters=false;
    protected $_pantonFace=false;
    protected $_pantonBack=false;
    protected $_shipping=false;
//    protected $_oplata=false;
    public $isMOrdered=false;
    public $isMOrderedTitle='Материал не заказан!';
    public $runBeforSave=true;//Выполнять подготовку к сохранению
    public $saveUpdateTime=true;//Сохранить время обновления
    public $fileInputUpLoad=null;
    const _stages=['Согласование','У дизайнера','Печать','Готов','Сдан'];
    const _payments=['Неоплачен','Предоплата','Оплачен'];
    const _ppFace=[
            '0','CMYK','1','2','3','4','5','6','7','8','9',
            'CMYK+1','CMYK+2','CMYK+3','CMYK+4','CMYK+5','CMYK+6','CMYK+7','CMYK+8','CMYK+9'
            ];
    const _ppBack=[
            '0','CMYK','1','2','3','4','5','6','7','8','9',
            'CMYK+1','CMYK+2','CMYK+3','CMYK+4','CMYK+5','CMYK+6','CMYK+7','CMYK+8','CMYK+9'
            ];
    const _mass=[
            'offsetlak'=>'Офсетный лак',
            'laminat'=>'Ламинация',
            'uflak'=>'Уф-лак',
            'termo'=>'Термоподъем',
            'tisnenie'=>'Тиснение',
            'kongrev'=>'Конгрев',
            'vyrubka'=>'Вырубка',
            'bigovka'=>'Биговка',
            'faltsovka'=>'Фальцовка',
            'skreychsloy'=>'Скрейч-слой',
            'skleyka'=>'Склейка',
            'skrepka'=>'Скрепка',
            'prujina'=>'Пружина',
            'pereplet'=>'Переплет',
            'kachirovka'=>'Кашировка',
            'plotrezka'=>'Плоттерная резка',
            'numeracia'=>'Нумерация',
            'prochee'=>'Прочее',
        ];
    const _defDatePickerOption=[
            'changeYear'=>'true',
            'changeMonth'=>'true',
            'gotoCurrent'=>'true',
//            selectOtherMonths=>true,
            'showOtherMonths'=>'true',
            'currentText'=>'Сегодня',
            'closeText'=>'X',
            'showButtonPanel'=>'true',
            'gotoCurrent'=>false,
            'selectOtherMonths'=>true,
            
            //defaultDate:d
        
    ];
    const _workTypes=['Не выбрано','Шёлк','100%','50 на 50'];
    const _paymentMethod=['№ счета','Договорная','В/з'];
    private $_MDetails=null;
    private $_MDetailsComments=null;
    private $_DetaliFormatLista=null;
    private function checkShipping(){
        if (!$this->_shipping){
            if (is_string($this->shipping)){
                $this->_shipping=Json::decode($this->shipping);
                if (!isset($this->_shipping['oplata'])) $this->_shipping['oplata']=[];
                if (!isset($this->_shipping['otgruzka'])) $this->_shipping['otgruzka']=[];
            }elseif(!is_array ($this->shipping)){
                $this->_shipping=[
                    'oplata'=>[],
                    'otgruzka'=>[]
                ];
            }else{
                $this->_shipping=$this->shipping;
            }
        }
    }
    abstract public function publishZakazFile($name,$desiner=false);
    public function getCustomerName(){ //Заказчик
        if ($this->customerManager && $tmp=Manager::findOne(['id'=>$this->customerManager])){
            return $tmp->firm_id;
        }else
            return 0;
    }
    public function getDateOfAdmissionFormated(){
        return \yii::$app->formatter->asDate($this->dateOfAdmission);
    }
    public function getFormatProduct(){
        return ArrayHelper::getValue($this->getParam(),0);
    }
    public function getWorkTypeTxt(){
        return self::_workTypes[$this->workType];
    }
    public function getDetaliSpecificationName(){
        $tmp=$this->mDetails;
        $val=isset($tmp['value'])?$tmp['value']:null;        
        if ($val){
            return \yii::t('app',$val[0]);
        }else{
            return 'Спецификация';
        }
    }
    private function createSpecificationFieldName($name){
        return \yii::t('app',$name);
    }
    private function createSpecification($val){
        $rVal=[];
        foreach ($val as $key=>$elVal){
            if (in_array($key, \yii::$app->db->schema->tableNames)){
                $tmpTble=new MaterialsNew(['tblName'=>$key]);
                if ($tmpEl=$tmpTble->findOne(['id'=>$elVal])){
                    $rVal[$key]=$tmpEl->name;
                }
            }else{
                switch ($key){
                    case 'supplierType':
                        $rVal[$key]=\yii::t('app','supplierTypeArray')[$elVal];
                        break;
                    case 'supplier':
                        if ($firm=Firms::findOne($elVal)){
                            $rVal[$key]=$firm->name;
                        }else{
                            $rVal[$key]='Не найден';
                        }
                        break;
                    default :
                        $rVal[$key]=$elVal;
                        break;
                }
            }
        }
        return $rVal;
    }
    private function createAttr(&$attr,$key,$elVal){
        if ($elVal===null) return false;
        $attr[$key]=['attribute'=>$key];
        
        switch ($key){
            case 'summ':
            case 'priceppc':
                $attr[$key]['format']='currency';
                break;
            case 'count':
                $attr[$key]['format']='text';
                $attr[$key]['value']=$elVal.' шт.';
                break;
            case 'density':
                $attr[$key]['format']='text';
                $attr[$key]['value']=$elVal.' г/м.кв';
                break;
            case 'dateOfOrder':
                if ($elVal){
                    $attr[$key]['format']='date';
                    $attr[$key]['contentOptions']=['class'=>'bg-warning'];
                    $attr[$key]['captionOptions']=['class'=>'bg-warning'];
                }else{
                    $attr[$key]['value']='не заказан';
                }
                break;
            case 'dateOfGet':
                if ($elVal){
                    $attr[$key]['format']='date';
                    $attr[$key]['contentOptions']=['class'=>'bg-success'];
                    $attr[$key]['captionOptions']=['class'=>'bg-success'];
                    if (isset($attr['dateOfOrder'])) unset($attr['dateOfOrder']);
                }else{
                    $attr[$key]['value']='не получен';
                }
                break;
            default :
                $attr[$key]['format']='text';
                break;
        }
        return true;
    }
    public function getDetaliSpecification(){
        $tmp=$this->getMDetails(true);
        $rVal=Html::beginTag('div',['class'=>'row']);
        $val=isset($tmp['value'])?$tmp['value']:null;
        $firstOpt=['class'=>'col-xs-3 table table-striped table-bordered detail-view'];
        $hide=\yii::$app->user->identity->role==='desiner'||\yii::$app->user->identity->role==='proizvodstvo';
        //unset($val[0]);
        if ($val){
            for ($i=1;$i<count($val);$i++){
                $valArray=$this->createSpecification($val[$i]);
                $dtV=[];
                $attr=[];
                Yii::trace(\yii\helpers\VarDumper::dumpAsString($valArray),'Zakaz::getDetaliSpecification');
                foreach ($valArray as $key=>$elVal){
                    Yii::trace("key($key)=>'$elVal'",'Zakaz::getDetaliSpecification');
                    if (!$hide||($key!=='summ'&&$key!=='priceppc')){
                        $dtV[$key]=$elVal;
                        if ($this->createAttr($attr, $key, $elVal))
                            if (array_key_exists($key, $tmp['colComment'])){
                                $attr[$key]['label']=$tmp['colComment'][$key];//$elVal;
                            }else{
                                $attr[$key]['label']=$this->createSpecificationFieldName($key);
                            }
                    }
                }
                
                if ($i==3){
                    $rVal.=Html::endTag('div').Html::beginTag('div',['class'=>'row']);
                }
                if ($tmpDt=  ArrayHelper::remove($attr, 'dateOfGet'))
                    $attr['dateOfGet']=$tmpDt;
                if ($tmpDt=  ArrayHelper::remove($attr, 'dateOfOrder'))
                    $attr['dateOfOrder']=$tmpDt;
                Yii::trace(\yii\helpers\VarDumper::dumpAsString($attr),'Zakaz::getDetaliSpecification');
                if (!isset($attr['dateOfOrder'])&&!isset($attr['dateOfGet'])){
                    $attr['dateOfOrder']=[
                        'attribute'=>'dateOfOrder',
                        'format'=>'text',
                        'value'=>'Нет',
                        'contentOptions'=>['class'=>'bg-danger'],
                        'captionOptions'=>['class'=>'bg-danger'],
                        'label'=>$this->createSpecificationFieldName('dateOfOrder')
                    ];
                }
                $rVal.=\yii\widgets\DetailView::widget([
                    'model'=>$dtV,
                    'attributes'=> $attr,
                    'options'=>$firstOpt
                ]);
                //Html::removeCssClass($firstOpt, 'pull-left');
        }
        }else{
            return 'Не задана';
        }
        $rVal.=Html::endTag('div');
        return $rVal;
    }
    public function getDetaliPribil(){
        $mater=$this->getMDetails(true);
        $execCoast=$this->getExecCoast();
        return $this->totalCost-$mater['necessarySumm']-$execCoast['payments'];
    }
    public function getDetaliPayments(){
        $mater=$this->getMDetails(true);
        $execCoast=$this->getExecCoast();
        return $mater['necessarySumm']+$execCoast['payments'];
    }
    public function getDetaliFormat_Size(){
        $mater=$this->getMDetails();
        if (isset($mater['value'])){
            if (isset($mater['value'][1]['productsize'])){
                return $mater['value'][1]['productsize'];
            }elseif(isset($mater['value'][1])){
                //$tmpTble=new MaterialsNew(['tblName'=>'sizes']);
                $tmpTble=MaterialsNew::crateObject('sizes');
                if ($el=$tmpTble->findOne($mater['value'][1]['sizes'])){
                    return $el->name;
                }else{
                    return 'Ошибка';
                }
            }else
                return 'Не задан';
        }else
            return 'Не задан';
    }
    private function checkDefaultFormat($txt){
        $rVal=false;
        switch ($txt){
            case 'a3':
                $rVal['width']='42';
                $rVal['height']='29';
                break;
            case 'a4':
                $rVal['width']='29';
                $rVal['height']='21';
                break;
            case 'a5':
                $rVal['width']='21';
                $rVal['height']='15';
                break;
        }
        return $rVal;
    }
    private function chankStr($txt){
        $posD1=mb_strpos($txt, '*');
        $str1=  mb_strcut($txt, 0,$posD1);
        $str2=  mb_strcut($txt, $posD1+1,mb_strlen($txt)-$posD1-1);
        if ($space=mb_strpos($str1,' ')){
            $str1=mb_strcut($str1, $space+1,mb_strlen($str1)-$space-1);
        }
        if ($space=  mb_strpos($str2, ' ')){
            $str2=  mb_strcut($str2, 0,$space);
        }
        if ((double)$str2>(double)$str1)
            return [
                'width'=>$str2,
                'height'=>$str1
            ];
        else
            return [
                'width'=>$str1,
                'height'=>$str2
            ];
    }
    private function DetaliCreatFormatSizes($txt){
        if ($rVal=$this->checkDefaultFormat(mb_strtolower($txt))){
            return $rVal;
        }else{
            preg_match('/\((.+)\)/', $txt, $rVal);
            if (count($rVal)>1){
                $rVal=$this->chankStr($rVal[1]);
                return $rVal;
            }elseif(count($rVal)==1){
                $rVal=$this->chankStr($rVal[0]);
                return $rVal;
            }else{
                return $this->chankStr($txt);
            }
        }
    }
    public function getDetaliFormatLista(){
        if ($this->_DetaliFormatLista) return $this->_DetaliFormatLista;
        $rVal=[
            'width'=>'Ширина',
            'height'=>'Высота'
        ];
        if (!$this->_MDetails) $this->getMDetails();
        if (isset($this->_MDetails['value'])){
            if (isset($this->_MDetails['value'][0])){
                if (in_array($this->_MDetails['value'][0],['paper','plastic'])){
                    $tmpTble=new MaterialsNew(['tblName'=>'sizes']);
                    if ($el=$tmpTble->findOne($this->_MDetails['value'][1]['sizes'])){
                        //$rVal=$this->DetaliCreatFormatSizes($el->name);
                        $rVal=$this->DetaliCreatFormatSizes($el->name);
                    }else{
                        $rVal['height']='Ошибка';
                        $rVal['width']='Ошибка';
                    }                    
                }
            }
        }
        $this->_DetaliFormatLista=$rVal;
        return $rVal;
    }
    public function getDetaliPaperName($elInd=1){
        $mater=$this->getMDetails();
        if (isset($mater['value'])){
            if (isset($mater['value'][0])){
            if ($mater['value'][0]==='paper'){
                if (isset($mater['value'][$elInd])){
                    $tmpTble=new MaterialsNew(['tblName'=>'paperName']);
                    if ($el=$tmpTble->findOne($mater['value'][$elInd]['paperName'])){
                        $rVal=$el->name;
                        unset($tmpTble);
                        unset($el);
                        if ($mater['value'][$elInd]['pcolors']){
                            if ($el=Pcolors::findOne($mater['value'][$elInd]['pcolors']))
                                $rVal.=' '.$el->name;
                        }
                        return $rVal;
                    }else{
                        return 'Ошибка';
                    }
                }else{
                    return 'Ошибка: ошибоч. инд.';
                }
            }else
                return 'Нет';
            }else return 'Нет';
        }else
            return 'Не задана';
    }
    public function getDetaliDensity($elInd=1){
        $mater=$this->getMDetails();
        if (isset($mater['value'])){
            if (isset($mater['value'][$elInd])){
                if (!isset($mater['value'][$elInd]['density'])) return 'Нет';
                $tmpTble=new MaterialsNew(['tblName'=>'density']);
                if ($el=$tmpTble->findOne($mater['value'][$elInd]['density'])){
                    return $el->name;
                }else{
                    return 'Ошибка';
                }
            }else
                return 'Нет';
        }else
            return 'Нет';
    }
    public function getDetaliCount($elInd=1){
        $mater=$this->getMDetails();
        if (isset($mater['value'])){
            if (isset($mater['value'][$elInd])){
                    if (isset($mater['value'][$elInd]['count'])){
                        return $mater['value'][$elInd]['count'];
                    }else{
                        return 'Нет';
                    }
            }else
                return 'Нет';
        }else
            return 'Нет';
    }
    public function getDetaliProductFormat(){
        if (!$this->_parameters) $this->getParam ();
        return $this->_parameters[0];        
    }
    public function getDetaliBlockFormat(){
        if (!$this->_parameters) $this->getParam ();
        return $this->_parameters[1];
    }
    public function getDetaliBlockCount(){
        if (!$this->_parameters) $this->getParam ();
        return $this->_parameters[3];
    }
    public function getDetaliUfLak(){
        if (!$this->_parameters) $this->getParam ();
        return isset($this->_parameters['post']['uflak'])?$this->_parameters['post']['uflak']:'';
    }
    public function getDetaliGetFaceTypeText(){
        if (!$this->_parameters) $this->getParam ();
        $tmp=self::_ppFace; //Глючит в NetBeans
        return $tmp[$this->_parameters['faceTypeId']];
    }
    public function getDetaliGetBackTypeText(){
        if (!$this->_parameters) $this->getParam ();
        $tmp=self::_ppBack; //Глючит в NetBeans
        return $tmp[$this->_parameters['backTypeId']];
    }
    public function getDetaliFaceContent(){
        if (!$this->_parameters) $this->getParam ();
        $rVal='';
        if (isset($this->_parameters['pantonFace'])){
            foreach($this->_parameters['pantonFace']as $val){
                if ($val){
                    if ($rVal)
                        $rVal.=',&nbsp;&nbsp;'.$val;
                    else
                        $rVal=$val;
                }
            }
        }
        return $rVal?$rVal:'Нет';
    }
    public function getDetaliBackContent(){
        if (!$this->_parameters) $this->getParam ();
        $rVal='';
        if (isset($this->_parameters['pantonBack'])){
            foreach($this->_parameters['pantonBack']as $val){
                if ($val){
                    if ($rVal)
                        $rVal.=',&nbsp;&nbsp;'.$val;
                    else
                        $rVal=$val;
                }
            }
        }
        return $rVal?$rVal:'Нет';
    }
//    public function getDetaliPospechat(){
    public function getDetaliPospechat2($url=''){
        if (!$this->_parameters) $this->getParam ();
        $rVal='';
        $tmpArr=self::_mass;
        if (isset($this->_parameters['post'])){
            if (count($this->_parameters['post'])){
                foreach ($this->_parameters['post'] as $key=>$val){
                    $rVal.='<tr>';
                    $rVal.='<th width="150px" colspan="2">'.$tmpArr[$key].'</th><td colspan="7">'.$val.'</td>';
                    $rVal.='</tr>';
                }
            }
        }
        return $rVal;
    }
    public function getDetaliFWork($elId=1){
        $rVal='';
        $rVal.='<tr class="hidden-print"><td colspan="9">';
        $rVal.=\app\widgets\FileListDropdown::widget([
            'id'=>'fileDD'.$elId,
            'zakazId'=>$this->id,
            'afterClickFunction'=>'onDetaliSelFileClick',
            'options'=>[
                'contid'=>'#ajaxCont'.$elId
            ],
            'size'=>'-sm'
        ]);
        $rVal.='</td>';
        $rVal.='<tr><td id="ajaxCont'.$elId.'" class="content-img" colspan="9">';
        $rVal.=$this->thumbNZakazFile();
        $rVal.='</td>';
        return $rVal;
    }
    public function getDetaliMaterialCount(){
        $mater=$this->getMDetails();
        if (!count($mater['value'])) return 0;
        if (isset($mater['value'][0])){
            Yii::trace(\yii\helpers\VarDumper::dumpAsString($mater['value'][0]),'_Zakaz::getDetaliMaterialCount');
            if ($mater['value'][0]==='paper'){
                Yii::info(\yii\helpers\VarDumper::dumpAsString($mater['value']),'_Zakaz::getDetaliMaterialCount');
                return count($mater['value'])-1;
            }else
                return 1;
        }else 
            return 1;
    }
    public function getInfoPart(){
        if (!$this->_parameters) $this->getParam ();
        $rVal='';
        $rVal.='<tr><th>Резка бумаги</th></tr>';
        $rVal.='<tr><td><div id="cutPut" class="liff"></div><div id="cutPut" class="liff"></div></td></tr>';
        return Html::tag('table',$rVal,['class'=>'table table-bordered table_i']);
    }
    public function thumbNZakazFile($name=false,$name2=false,$desiner=false){
        if (!$name) $name=\yii::$app->request->post('showFileName');
        if (!$name){
            $f1='Не выбран';
        }else{
            $f1=$this->thumbNZakazFileCreate($name);
        }
        return $f1;
    }
    public function thumbNZakazFileCreate($name,$desiner=false){
        $w=450;
        $h=790;
        if ($fParam=$this->publishZakazFile($name, $desiner)){
            if (in_array($fParam['ext'], ['jpg','tif','jpeg','pn­g','ai','eps'])){
                $wh=[];
                $img= ImgProcess::prepareImg($fParam['path'],$w,$h,$wh);
                $wd=$w/$wh['width'];
                $options=['class'=>'img-thumbnail'];
                if ($wh['height']*$wd<$h) $options['width']=$w;else $options['height']=$h;
                return Html::img($img,$options);
            }elseif($fParam['ext']==='doc'){
                $tmp3= \yii::$app->params['extHost'].$fParam['url'];
                $src='http://docs.google.com/gview?url='.$tmp3.'&a=bi&pagenumber=1&embedded=true';
                return Html::tag('iframe',null,[
                    'src'=>$src,
                    'width'=>$w,
                    'height'=>$h,
                    'style'=>'border: none;'
                ]);
            }elseif($fParam['ext']==='pdf'){
                $wh=[];
                $img= ImgProcess::convertPDF($fParam['path'],$w,$h,$wh);
                $wd=$w/$wh['width'];
                $options=['class'=>'img-thumbnail'];
                if ($wh['height']*$wd<$h) $options['width']=$w;else $options['height']=$h;
                return Html::img($img,$options);
            }else
                return Html::tag('div','Тип файла "'.$fParam['ext'].'" не поддерживается.');
        }else{
            return Html::tag('div','Нет');
        }
    }
    public function getShippingOplata(){
        $this->checkShipping();
        if (!isset($this->_shipping['oplata']))$this->_shipping['oplata']=[];
        return $this->_shipping['oplata'];
    }
    public function getShippingOtgruzka(){
        $this->checkShipping();
        if (!isset($this->_shipping['otgruzka']))$this->_shipping['otgruzka']=[];
        return $this->_shipping['otgruzka'];
    }
    public function processPayment($changeP=true){
        $opl=$this->getShippingOplata();
        $isSet=false;
        (double)$summ=0;
        $rVal=$this->payment;
        if (count($opl)){
            $tmp=$opl;
            foreach ($tmp as $ind=>$el){
                if (isset($el['summ'])){
                    //if ($changeP) {echo '<br>';\yii\helpers\VarDumper::dump($el,10,true);}
                    if (!$isSet) $isSet=$el['summ']!==''&&$el['summ']!==null;
                    if ($el['summ'])
                        $summ+=(double)$el['summ'];
                }
            }
            if ($isSet){
                if ($summ){
                    if ($summ<$this->totalCost){
                        if ($this->payment===0){
                            if ($changeP){
//                                if ($changeP)echo 'payment: '.\yii\helpers\VarDumper::dumpAsString($this->payment).' to '. 1;
                                $this->payment=1;
                            }
                            $rVal=1;
                        }
                    }else{
                        //if ($this->payment===1||$this->payment===0){
                            if ($changeP){
//                                if ($changeP)echo 'payment: '.\yii\helpers\VarDumper::dumpAsString($this->payment).' to '. 2;
                                $this->payment=2;
                            }
                            $rVal=2;
                        //}
                    }
                }
            }
        }
//        if ($changeP)Yii::$app->end();
        return $rVal;
    }

    public function getPaymentStage(){
        $tmp=['Неоплачен','Предоплата','Оплачен'];
        if ($this->payment===null)
            return 'Не задано';
        else
            return $tmp[$this->processPayment(false)];
    }
    public function getStageTxt(){
        $tmp=['Согласование','У дизайнера','Печать','Готов','Сдан'];
        if ($this->stage===null)
            return 'Не задано';
        else
            return $tmp[$this->stage];
    }
    public function getOrderTypeTxt(){
        if ($tmp=OrdersNames::findOne(['id'=>$this->orderType]))
            return $tmp->name;
        else
            return '';
    }
    //public function getIsNewRecord(){return true;}
    public function getTbluser(){
        return $this->hasOne(TblUser::className,['id'=>'managerId']);
    }
    public function getOManager($mId=false){
        $user=Yii::$app->user->identity;
        if ($this->isNewRecord){
            return (!$user->realname?$user->username:$user->realname);
        }else{
            if ($model=TblUser::findOne(['id'=>$mId?$mId:$this['managerId']]))
                return $model->realname;
            else
                return (!$user->realname?$user->username:$user->realname);
        }
    }
//    public function setOManager($val){
//        
//    }
    public function MetodIspolnenyTextByID($id){
        if ($model=MethodOfExecution::findOne($id)){
            return $model->name;
        }  else {
            return 'Ненайден';
        }
    }
    public function getMetodIspolneny(){
        //method_of_execution
        return \yii\helpers\ArrayHelper::map(MethodOfExecution::find()->orderBy(['name' => SORT_ASC])->asArray()->all(),'id','name');
    }
    public static function getZakazTypes(){
        return \yii\helpers\ArrayHelper::map(OrdersNames::find()->orderBy(['name' => SORT_ASC])->asArray()->all(),'id','name');  
    }
    public function getManager(){
        return $this->hasOne(Manager::className(),['id'=>'customerManager']);
    }
    public function getManagerById($id){
        $tmp=Manager::findOne(['id'=>$id]);
        if ($tmp)
            return $tmp->name;
        else
            return 'Не найден';
    }
    public static function getFirmsListNew($firmId=0){
        $tmp=Firms::find()->andFilterWhere(['like', 'firmtype',$firmId])->orderBy(['name' => SORT_ASC])->select(['id','name'])->asArray()->all();
        $rVal=[];
        foreach ($tmp as $it){
            $rVal[$it['id']]=[
                'label'=>$it['name'],
                'url'=>'#',
                'linkOptions'=>['value'=>$it['id'],'role'=>'menuItem','href'=>'#','tabindex'=>-1]
            ];
        }
        $rVal[]=[
            'label'=>'Добавить...',
            'url'=>'#',
            'linkOptions'=>['value'=>-1,'role'=>'menuItem','href'=>'#','tabindex'=>-1]
            ];
//        Yii::$app->end();
        return $rVal;
    }
    public function getFirmIdByManager(){
        $rVal=0;
        if ($this->customerManager){
            if ($tmp=Manager::findOne(['id'=>$this->customerManager]))
                $rVal=$tmp->firm_id;
        }
        return $rVal;
    }
    public function getCurrentCustomerFirmName(){
       //\yii\helpers\VarDumper::dump($this->manager,10,true);
       //\yii::$app->end();
        if ($tmp=$this->manager)
            return $tmp->firms->name;
        else
            return '';
    }
    public static function getManagerList2($firmId){
        $tmp=Manager::find()->where(['firm_id'=>$firmId])->select(['id','name','middle_name','surname','fone'])->asArray()->all();
        $rVal=[];
        foreach ($tmp as $it){
            $lbl=$it['name'];
            if ($it['middle_name']!=null) $lbl.=' '.$it['middle_name'];
            if ($it['surname']!=null) $lbl.=' '.$it['surname'];
            $fone=\yii\helpers\Json::decode(\yii\helpers\ArrayHelper::getValue($it,'fone','{}'));
            if (count($fone)>0){
                $fone1=  array_shift ($fone);
            }else{
                $fone1='';
            }
            $rVal[$it['id']]=[
                'label'=>$lbl,
                'url'=>'#',
                'linkOptions'=>['value'=>$it['id'],'role'=>'menuItem','href'=>'#','tabindex'=>-1,'title'=>$fone1]
            ];
        }
        $rVal[]=[
            'label'=>'Добавить...',
            'url'=>'#',
            'linkOptions'=>['value'=>-1,'role'=>'menuItem','href'=>'#','tabindex'=>-1]
            ];
        return $rVal;
       
    }
    public function getExecCoast(){
        if ($this->contractors){
            return Json::decode($this->contractors);
        }else
            return [
                'summ'=>0,
                'payments'=>0,
                'profit'=>0,
                'superprofit'=>0
            ];
    }
    public function setExecCoast(&$val){
        $this->execCoast=$val;
    }
    public function getParam(){
        if (!$this->_parameters){
            if (is_string($this->parameters)){
                $this->_parameters=Json::decode($this->parameters);
            }else
                $this->_parameters=[];
        }
        return $this->_parameters;
    }
    public function getMDetails($comment=false){
        if ($this->_MDetails&&!$comment){
            return $this->_MDetails;
        }elseif($this->_MDetailsComments){
            return $this->_MDetailsComments;
        }
        $rVal=[
            'necessarySumm'=>0,
            'totalSumm'=>0,
            'value'=>[]
        ];
        $dopParam=[];
        if (is_string($this->materialDetails)){
            $tmp=Json::decode($this->materialDetails);
            $rVal=[
                'necessarySumm'=>$tmp['necessarySumm'],
                'totalSumm'=>$tmp['totalSumm'],
                'value'=>[]
            ];
            unset($tmp['necessarySumm']);
            unset($tmp['totalSumm']);
            foreach ($tmp as $key=>$val){
                if($key!='value') $dopParam[$key]=$val;
            }
            //echo \yii\helpers\VarDumper::dumpAsString($tmp,10,true);
            if (isset($tmp[0])){
                $rVal[0]=$tmp[0];
                if (isset($tmp['value'])){
                        $rVal['value'][]=$tmp[0];
                        $model=MaterialsNew::crateObject($tmp[0]);
                        if ($comment){
                            $rVal['colComment']=[];
                            foreach ($model->getTableSchema()->columns as $key=>$col){
                                if ($key!=='id'){
                                    $rVal['colComment'][$key]=$col->comment;
                                }
                            }
                        }
                        if ($tmp[0]!=='paper'){
                            if (isset($tmp['value']['materialsIdList'])){
                                if ($tmp2=$model->findOne(['id'=>$tmp['value']['materialsIdList']])){
                                    $tmp['value']=  array_merge ($tmp['value'],$tmp2->readVFDT());
                                }
                                unset($tmp['value']['materialsIdList']);
//                                \yii\helpers\VarDumper::dump($this->isNewRecord,10,true);Yii::$app->end();
                                if (!isset($tmp['value']['dateOfOrder'])||$this->isNewRecord) $tmp['value']['dateOfOrder']=null;
                                if (!isset($tmp['value']['dateOfGet'])||$this->isNewRecord) $tmp['value']['dateOfGet']=null;
                                if (isset($tmp['value']['payed'])&&$this->isNewRecord) unset($tmp['value']['payed']);
                                $rVal['value'][]=ArrayHelper::merge($tmp['value'],$dopParam);
                            }
                        }elseif (is_array($tmp['value'])){
                            foreach ($tmp['value'] as $el){
                                if (isset($el['materialsIdList'])){
                                    if ($tmp2=$model->findOne(['id'=>$el['materialsIdList']])){
                                        $el=array_merge ($el,$tmp2->readVFDT());
                                    }
                                    unset($el['materialsIdList']);
//                                    \yii\helpers\VarDumper::dump($this->isNewRecord,10,true);Yii::$app->end();
                                    if (!isset($el['dateOfOrder'])||$this->isNewRecord) $el['dateOfOrder']=null;
                                    if (!isset($el['dateOfGet'])||$this->isNewRecord) $el['dateOfGet']=null;
                                    if (isset($el['payed'])&&$this->isNewRecord) unset($el['payed']);
                                    $rVal['value'][]=$el;
                                }
                            }
                        }
                        
                }
            }
        }
        if (!$comment)
            $this->_MDetails=$rVal;
        else
            $this->_MDetailsComments=$rVal;
        \yii::trace(\yii\helpers\VarDumper::dumpAsString($rVal,10),'material');
        return $rVal;
    }
    public function faceBackCount($id,$arr){
        $val=ArrayHelper::getValue($arr,$id,'0');
        $tmp=substr($val, strlen($val)-1);
        return $tmp;
        if (is_integer($tmp))
            return (int)$tmp;
        else
            return 0;
    }
    public function faceTypeCount(){
        $faceTypeId=ArrayHelper::getValue($this->param,'faceTypeId',false);
        if ($faceTypeId!==false){
            return $this->faceBackCount($faceTypeId,self::_ppFace);
        }else{
            return 0;
        }
    }
    public function backTypeCount(){
        $backTypeId=ArrayHelper::getValue($this->param,'backTypeId',false);
        if ($backTypeId!==false){
            return $this->faceBackCount($backTypeId,self::_ppBack);
        }else{
            return 0;
        }        
    }
    public function getStageTxtDesiner(){
        if ($this->stage==1){
            return Html::button($this->getStageTxt(),[
                'class'=>'btn btn-primary btn-xs prDone',
                'title'=>"Заказ №$this->id изменить на (в печать)",
                'newstagetext'=>'В печать',
                'name'=>'stage',
                'back'=>'1',
                'value'=>'2'
            ]);
        }elseif($this->stage==0){
            return Html::button($this->getStageTxt(),[
                'class'=>'btn btn-warning btn-xs prDone',
                'title'=>"Заказ №$this->id изменить на (у дизайнера)",
                'newstagetext'=>'У дизайнера',
                'name'=>'stage',
                'back'=>'0',
                'value'=>'1'
            ]);
            
        }else
            return $this->getStageTxt ();
    }
    public function getStageTxtProizvodstvo(){
        return Html::button($this->getStageTxt(),[
            'class'=>'btn btn-primary btn-xs prDone',
            'title'=>"Заказ №$this->id изменить на (готов)",
            'name'=>'stage',
            'back'=>'2',
            'value'=>'3',
            'data-placement'=>"left"
        ]);
    }
//    protected function checkOplata(){
//        if (!$this->_oplata){
//            if ($this->oplata!==null){
//                $this->_oplata= Json::decode($this->oplata);
//            }else{
//                $this->_oplata=[
//                    'executers'=>[],
//                    'material'=>[]
//                ];
//            }
//        }
//        return $this->_oplata;
//    }
//    public function getOplataStatus(){
//        return $this->checkOplata();
//    }
}
