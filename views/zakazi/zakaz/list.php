<?php
//use \Yii;
use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Zakaz;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
        . 'rqName:"list"'
        . '});',\yii\web\View::POS_READY,'zListStar');
$this->beginContent('@app/views/layouts/zakazLOUT.php');
$this->title='Заказы';
$this->params['breadcrumbs'][] = $this->title;
$columns=[
            'special_attention'=>[
                'content'=>function ($model, $key, $index, $column){
                    //return Html::tag('span',null,['class'=>'glyphicon glyphicon-warning-sign']);
                    if ($model->special_attention) 
                        return Html::tag('span',null,[
                            'class'=>'glyphicon glyphicon-warning-sign text-danger',
                            'title'=>$model->special_attention,
                            'data-placement'=>'right'
                        ]);
                    else 
                        return '';
                }
            ],
            'hasfile:html',
            ['class' => 'app\widgets\MActionColumn',
                'modelKeyToConfirm'=>'name',
//                'confirm'=>'Удалить заказ "{info}" ?',
                'copyConfirm'=>'Скопировать заказ "{info}" ?',
                'checkUserIdColName'=>Yii::$app->user->identity->role!=='admin'?'managerId':false,
                'otherParam'=>$this->context->zakazUrlOption(),
                'template'=>'{details} {change} {copy} {remove}'
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
                //'attribute'=>'searchAdmission',
                //'form'=>'',
                'name'=>$searchModel->formName().'[searchAdmission]',
                'value'=>$searchModel->searchAdmission
            ])
            ,'Дата приёма'),
            Zakaz::createSF('searchFirm','text','currentCustomerFirmName',Zakaz::createCustomerListForFilter(),'Заказчик'),
            Zakaz::createSF('searchOrderType','text','orderTypeTxt',Zakaz::getZakazTypes()),
            //'dateOfAdmission:date',
            Zakaz::createSF('name'),
            //'name',

            //'orderTypeTxt:text',
            Zakaz::createSF('numberOfCopies','text'),
            //'numberOfCopies:integer',
            Zakaz::createSF('searchStageId','text','stageTxt',  Zakaz::_stages),
            //'stageTxt:text',
            Zakaz::createSF('searchDeadline','date','deadline',\yii\jui\DatePicker::widget([
                'language' => 'ru',
                'dateFormat' =>'dd.MM.yyyy',
                'options'=>['class'=>'form-control'],
                //'attribute'=>'searchAdmission',
                //'form'=>'',
                'name'=>'ZakazSearch[searchDeadline]',
                'value'=>$searchModel->searchDeadline
            ])),
            //'deadline:date',
            Zakaz::createSF('totalCost',['decimal',0],false,false,'Сумма'),
            Zakaz::createSF('searchPayment','text','paymentStage',Zakaz::_payments,'Оплата'),
        ];
        Yii::info(\yii\helpers\VarDumper::dumpAsString($dataProvider->getModels()),'z-main');
?>
    <?php if ($this->beginCache(Yii::$app->id.'listUpKeys',['duration'=>3600])):?>
    <div class="form-group">
        <div class="input-group input-group-sm" style="width: 400px;" role="group" >
            <div class="input-group-btn">`
                <?= Html::a('Добавить', ['add'], ['class' => 'btn btn-success']) ?>
                <?= Html::a('Найти заказ №', ['find','actionId'=>$this->context->action->id], ['class' => 'btn btn-default','id'=>'zakazFind']) ?>
            </div>
            <?=Html::textInput('',null,['class'=>'form-control','id'=>'zakazFindNum'])?>
            <span class="form-control-feedback" aria-hidden="true"><?=$img?></span>
        </div>
        <p class="help-block help-block-error"></p>
    </div>
    <?php $this->endCache();?>
    <?php endif;?>
    <?php Pjax::begin([
        'id'=>'listPjax',
        ])?>
    <?= GridView::widget([
        'id'=>'listZakazov',
        'summary'=>false,//'<span>Показаны с {begin} по {end} Всего {totalCount}'.$img.'</span>',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterRowOptions'=>['class'=>'hidden'],
        'columns' => $columns,
        'pager'=>[
            'class'=>'app\widgets\MLinkPager',
            'firstPageLabel'=>'&laquo;&laquo;&laquo;',
            'lastPageLabel'=>'&raquo;&raquo;&raquo;',
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