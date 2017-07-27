<?php
use \Yii;
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
//$isProizv;
//Yii::$app->end();
global $globisProizv;
$globisProizv=$isProizv;
$this->title=!$isProizv?'Дизайнер':'Производство';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile($this->assetManager->publish('@app/web/js/zakazList.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listZakaz');
$this->registerJsFile($this->assetManager->publish('@app/web/js/dialog.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listDialog');
$img=Html::img($this->assetManager->publish('@app/web/pic/loader-small.gif')[1],[
    'height'=>'20px',
    'width'=>'20px',
    'alt'=>'Загрузка'
    ]);
$img=Html::tag('span',$img.'<span>Загрузка</span>',['id'=>'loader']);
$this->registerJs('var Zakaz= new ZakazLisController('
        . '{'
        . 'ajaxupdaterequest:"'.Url::to(['ajaxupdaterequest']).'",'
        . 'rqVarName:"rq",'
        . 'rqName:"list"'
        . '});',\yii\web\View::POS_READY,'zListStar');
$this->beginContent('@app/views/layouts/zakazLOUT.php');
$otherParam=[];
//if ($isDesiner) 
    
if ($isProizv)
    $otherParam['isProizv']=true;
else
    $otherParam['isDesiner']=true;
$columns=[
        'hasfile:html',
        ['class' => 'app\widgets\MActionColumn',
            'modelKeyToConfirm'=>'name',
            'confirm'=>'Удалить заказ "{info}" ?',
            'copyConfirm'=>'Скопировать заказ "{info}" ?',
            'checkUserIdColName'=>Yii::$app->user->identity->role!=='admin'?'managerId':false,
            'otherParam'=>$this->context->zakazUrlOption(false,$otherParam),
            'template'=>'{details} {deschange}{change} {copy} {remove}'
        ],

        'id'=>[
            'content'=>function ($model, $key, $index, $column){
                return Html::tag('span',Html::tag('a',$key,['name'=>$key]),['class'=>'GDA']);
            },
            'attribute'=>'id',
        ],
        Zakaz::createSF('searchManagerId','text','oManager',Zakaz::oManagerList(),null,['man'=>'manager']),
        Zakaz::createSF('searchAdmission','date','dateOfAdmission',\yii\jui\DatePicker::widget([
            'language' => 'ru',
            'dateFormat' =>'dd.MM.yyyy',
            'options'=>['class'=>'form-control','role'=>'datepicker'],
            'name'=>$searchModel->formName().'[searchAdmission]',
            'value'=>$searchModel->searchAdmission
        ])
        ,'Дата приёма'),
        Zakaz::createSF('searchFirm','text','currentCustomerFirmName',Zakaz::createCustomerListForFilter(),'Заказчик'),
        Zakaz::createSF('searchOrderType','text','orderTypeTxt',Zakaz::getZakazTypes()),
        Zakaz::createSF('name'),
        //Zakaz::createSF('stage','html','stageTxtProizvodstvo'),
        [
            'attribute'=>'stage',
            'content'=>function ($model){
                global $globisProizv;
                if ($globisProizv){
                    return $model->stageTxtProizvodstvo;
                }else{
                    return $model->stageTxtDesiner;
                }
            }
        ],
        Zakaz::createSF('numberOfCopies','text'),
        [
            'attribute'=>'deadline',
            'content'=>function($model){
                return $model->deadline?\yii::$app->formatter->asDate($model->deadline):'Не задана';
            },
            'format'=>'text'
        ]


    ];
?>
    <?php Pjax::begin([
        'id'=>'listPjax',
//        'clientOptions'=>['success'=>'Zakaz.init']
        ])?>
    <?= GridView::widget([
        'id'=>'listZakazovDes',
        'summary'=>false,//'<span>Показаны с {begin} по {end} Всего {totalCount}'.$img.'</span>',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterRowOptions'=>['class'=>'hidden'],
        'columns' => &$columns,
        'pager'=>[
            'class'=>'yii\widgets\LinkPager',
            'firstPageLabel'=>'&laquo;&laquo;&laquo;',
            'lastPageLabel'=>'&raquo;&raquo;&raquo;'
        ],
        'rowOptions'=>function ($model){
                    $rVal=[];
                    if ($model->totalCost==1){
                        $rVal['class']='cRed';
                    }
                    return $rVal;
        },
        'tableOptions'=>['class'=>'table table-striped table-hover']
    ]) ?>
    <?php Pjax::end()?>
<?php $this->endContent();?>