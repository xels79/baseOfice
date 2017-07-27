<?php
//use \Yii;
use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Zakaz;
use yii\helpers\ArrayHelper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$datePicOptions=$searchModel::_defDatePickerOption;
$datePicOptions['maxDate']=Yii::$app->formatter->asDate(time());

$this->registerJsFile($this->assetManager->publish('@app/web/js/zakazList.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listZakaz');
$this->registerJsFile($this->assetManager->publish('@app/web/js/dialog.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listDialog');
$img=Html::img($this->assetManager->publish('@app/web/pic/loader-small.gif')[1],[
    //'id'=>'loader',
    'height'=>'20px',
    'width'=>'20px',
    'alt'=>'Загрузка'
    ]);
$img=Html::tag('span',$img,['id'=>'loader']);
$this->registerJs('var Zakaz= new ZakazLisController('
        . '{'
        . 'ajaxupdaterequest:"'.Url::to(['ajaxupdaterequest']).'",'
        . 'rqVarName:"rq",'
        . 'rqName:"buglist",'
        . 'isBugalter:true,'
        . 'datePickerOptions:'.yii\helpers\Json::encode($datePicOptions)
        . '});',\yii\web\View::POS_READY,'zListStar');
$this->beginContent('@app/views/layouts/zakazLOUT.php');
$this->title='Заказы бухгалтерия';
$this->params['breadcrumbs'][] = $this->title;

$columns=[
            ['class' => 'app\widgets\MActionColumn',
                'otherParam'=>$this->context->zakazUrlOption(false,['isBugalter'=>true]),
                'template'=>'{details}'
            ],
            'id'=>[
                'content'=>function ($model, $key, $index, $column){
                    return Html::tag('span',Html::tag('a',$key,['name'=>$key]),['class'=>'GDA']);
                },
                'attribute'=>'id',
                'contentOptions'=>function ($model, $key, $index, $column){
                       return $model->GDStageContentOptions();
                   }
            ],
            Zakaz::createSF('searchManagerId','text','oManager',Zakaz::oManagerList(),null,['man'=>'manager']),
            Zakaz::createSF('searchAdmission','date','dateOfAdmission',\yii\jui\DatePicker::widget([
                'language' => 'ru',
                'dateFormat' =>'dd.MM.yyyy',
                'options'=>['class'=>'form-control','role'=>'datepicker'],
                'name'=>$searchModel->formName().'[searchAdmission]',
                'value'=>$searchModel->searchAdmission,
                'clientOptions'=> $datePicOptions
            ])
            ,'Дата приёма'),
            Zakaz::createSF('searchFirm','text','currentCustomerFirmName',Zakaz::createCustomerListForFilter(),'Заказчик'),
            Zakaz::createSF('searchWorkType','text','workTypeTxt',Zakaz::_workTypes),
            Zakaz::createSF('searchPaymentMethod','text',function ($model){
                if ($model->paymentMethod===0){
                    $nm=$model->accountNumber!='Не выставлено'?'№ ':'';
                    return $model->accountNumber?($nm.$model->accountNumber):'Не выставлено';
                }else
                    return $model::_paymentMethod[$model->paymentMethod];
            },$searchModel::_paymentMethod,'Спос. опл.',function ($model){
                if ($model->paymentMethod===0){
                    $nm=$model->accountNumber!='Не выставлено'?'№ ':'';
                    return [
                        'title'=>$model->accountNumber?($nm.$model->accountNumber):'Не выставлено'
                    ];
                }else
                    return [];
            }),
            Zakaz::createSF('totalCost',['decimal',0],false,false,'Сумма',null,Yii::$app->formatter->asDecimal($totalSumm,0)),
            Zakaz::createSF('searchPayment','text','paymentStage',Zakaz::_payments,'Оплата'),
            Zakaz::createSF('paymentMaterial','raw',function($model){
                $mater=$model->mDetails;
                $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                if ($value= \yii\helpers\ArrayHelper::getValue($mater,'value'))
                    if (is_array($value))
                        if ($cnt=count($value)){
                            $rVal='';
                            for ($i=1;$i<$cnt;$i++){
                                $rVal.=$model->renderMaterPayed($value[$i],$i);
                            }
                            if (!mb_strlen($rVal)) $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                        }
                return $rVal;
            },false,'Материалы',null,Yii::$app->formatter->asDecimal($summMat,0)),
            Zakaz::createSF('paymentExecutor','raw',function($model){
                $execCoast=$model->execCoast;
                $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                if (isset($execCoast['value']))
                    if (is_array($execCoast['value'])){
                        $rVal='';
                        foreach($execCoast['value'] as $index=>$el){
                            if ($el['payments']&&$el['idFirm']!=68){
                                $cont=Html::tag('span',Yii::$app->formatter->asDecimal($el['payments'],0),[]);
                                $opt=['requestfor'=>'executer','elnum'=>$index,'class'=>'td-raw','title'=>'Исполнитель: '.$model->firmNameById($el['idFirm']),'data-placement'=>'left'];//68
                                if ($payed=\yii\helpers\ArrayHelper::getValue($el,'payed',0))
                                    $checked=[
                                        'class'=>'glyphicon glyphicon-check',
                                        'title'=>Yii::$app->formatter->asDate($payed)
                                    ];
                                else
                                    $checked=['class'=>'glyphicon glyphicon-unchecked','role'=>'checkbexec'];
                                $cont.=Html::tag('span',null,$checked);
                                $rVal.= \yii\helpers\Html::tag('div',$cont,$opt);
                            }
                        }
                        if (!mb_strlen($rVal)) $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                    }
                    
                return $rVal;
            },false,'Исполнители',null,Yii::$app->formatter->asDecimal($summExec,0)),
            Zakaz::createSF('bonuses','raw',function($model){
                $execCoast=$model->execCoast;
                $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                if (isset($execCoast['value']))
                    if (is_array($execCoast['value'])){
                        $rVal='';
                        foreach($execCoast['value'] as $index=>$el){
                            if ($el['payments']&&$el['idFirm']==68){
                                $cont=Html::tag('span',Yii::$app->formatter->asDecimal($el['payments'],0),[]);
                                $opt=['requestfor'=>'executer','elnum'=>$index,'class'=>'td-raw','data-placement'=>'left','info-text'=>'Бонус'];//68
                                if ($payed=\yii\helpers\ArrayHelper::getValue($el,'payed',0))
                                    $checked=[
                                        'class'=>'glyphicon glyphicon-check',
                                        'title'=>Yii::$app->formatter->asDate($payed)
                                    ];
                                else
                                    $checked=['class'=>'glyphicon glyphicon-unchecked','role'=>'checkbexec'];
                                $cont.=Html::tag('span',null,$checked);
                                $rVal.= \yii\helpers\Html::tag('div',$cont,$opt);
                            }
                        }
                        if (!mb_strlen($rVal)) $rVal=Html::tag('div',Html::tag('span','&nbsp;').Html::tag('span',null,['class'=>'glyphicon glyphicon-check']),['class'=>'td-raw']);
                    }
                    
                return $rVal;

            },false,'Бонусы',null,Yii::$app->formatter->asDecimal($summBonus,0)),
            Zakaz::createSF('profit',['decimal',0],function($model){
                return $model->prib['summ'];
            },false,'Прибыль',null, Yii::$app->formatter->asDecimal($totalProfit,0)),
        ];
            //'maxDate'=>Yii::$app->formatter->asDate(date()),
?>
    <?php Pjax::begin([
        'id'=>'listPjax',
        'timeout'=>10000
    ])?>
    <div class='form-inline'>
        <div class='form-group form-group-sm'>
            <div class="input-group input-group-sm">
                <span class="input-group-btn">
                    <?= Html::a('Найти заказ №', ['find','actionId'=>$this->context->action->id], ['class' => 'btn btn-default','id'=>'zakazFind']) ?>
                </span>
                <?=Html::textInput('',null,['class'=>'form-control','id'=>'zakazFindNum'])?>
                <span class="form-control-feedback" aria-hidden="true"><?=$img?></span>
            </div>
        </div>
        
        <div class='form-group form-group-sm'>
            <div class="input-group input-group-sm">
                <span class="input-group-addon">
                    Показать от:
                </span>
                <?= \yii\jui\DatePicker::widget( [
                        'model'=>$searchModel,
                        'attribute'=>'bugstartdate',
                        'language' => 'ru',
                        'dateFormat' => 'dd.MM.yyyy',
                        'options'=>['class'=>'form-control','role'=>'tofilter'],
                        'clientOptions'=> !$searchModel->bugenddate?$datePicOptions:ArrayHelper::merge($datePicOptions, ['maxDate'=>$searchModel->bugenddate])
                ]) ?>
            </div>
        </div>
        <div class='form-group form-group-sm'>
            <div class="input-group input-group-sm">
                <span class="input-group-addon">
                    до:
                </span>
                <?= \yii\jui\DatePicker::widget( [
                        'model'=>$searchModel,
                        'attribute'=>'bugenddate',
                        'language' => 'ru',
                        'dateFormat' => 'dd.MM.yyyy',
                        'options'=>['class'=>'form-control','role'=>'tofilter'],
                        'clientOptions'=> !$searchModel->bugstartdate?$datePicOptions:ArrayHelper::merge($datePicOptions, ['minDate'=>$searchModel->bugstartdate])
                ]) ?>
            </div>
        </div>
        <?=Html::button('Сбросить',[
            'class'=>'btn btn-default btn-sm',
            'type'=>'button',
            'name'=>'resetDateSearch',
            'id'=>'reset-date-search'
        ])?>
    </div>
    <p class="help-block help-block-error" id="zakazFindMess"></p>
    <?= GridView::widget([
        'filterSelector'=>'[role=tofilter]',
        'id'=>'listZakazovBugalter',
        'summary'=>'<span>Показаны с {begin} по {end} Всего {totalCount}</span>',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterRowOptions'=>['class'=>'hidden'],
        'columns' => $columns,
        'showFooter'=>true,
        'pager'=>[
            'class'=>'app\widgets\MLinkPager',
            'lastPageLabel'=>'&raquo;&raquo;&raquo;',
            'firstPageLabel'=>'&laquo;&laquo;&laquo;',
            'selectPage'=>true
        ],
        'rowOptions'=>function ($model, $key, $index, $grid){
                    $rVal=[];
                    if ($model->totalCost==1||$model->deadline==='1970-01-01'||!$model->deadline){
                        $rVal['class']='cRed';
                    }
                    if ($model->id===$this->context->backId){
                        if (isset($rVal['class']))
                            $rVal['class']='lastEditcRed';
                        else
                            $rVal['class']='lastEdit';
                    }
                    $rVal['manName']=$model->getOManager();
                    return $rVal;
        },
        'tableOptions'=>['class'=>'table table-striped table-hover']
    ]) ?>
    <?php Pjax::end()?>
    <div class="dropdown">
    <?= \yii\bootstrap\Dropdown::widget([
        'id'=>'contMenu',
        'options'=>[
            'class'=>'content-menu-right'
        ],
        'items' => [
            '<li class="dropdown-header">Дата заказа материала</li>',
            '<li role="presentation" class="divider"></li>',
            ['label' => 'Изменить', 'url' => 'chgdt'],
            ['label'=>'Отменить','url'=>'canceldt']
        ]
    ])?>
    </div>
<?php $this->endContent();?>