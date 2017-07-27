<?php
use yii\bootstrap\Tabs;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Panel;
use yii\bootstrap\Modal;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$this->registerJsFile($this->assetManager->publish('@app/web/js/uloader.js')[1],[
//    //'depends' => [\yii\web\JqueryAsset::className()],
//    'position'=> yii\web\View::POS_READY
//],'addULoader');
//$this->registerJsFile($this->assetManager->publish('@app/web/js/mProgress.js')[1],[
//    //'depends' => [\yii\web\JqueryAsset::className()],
//    'position'=> yii\web\View::POS_READY
//],'addMProgress');
$this->registerJsFile($this->assetManager->publish('@app/web/js/otgruzki.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'addOtgruzki');
$this->registerJsFile($this->assetManager->publish('@app/web/js/zakaz.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'addZakaz');
$this->registerJsFile($this->assetManager->publish('@app/web/js/dmuploader.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'loader1');

//$this->registerJsFile($this->assetManager->publish('@app/web/js/uloader.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'loader2');
//$this->registerCssFile($this->assetManager->publish('@app/web/css/upload.less')[1],null,'loaderCss');
$this->registerJs('Zakaz=new ZakazController("custMan",'
        . '{selId:"zakaz-contractors-sel",'
        . 'tblId:"zakaz-contractors-tbl",'
        . 'dialogId:"askMoadal",'
        . 'ajaxupdaterequest:"'.Url::to(['ajaxupdaterequest']).'"'
        . '},"zakaz-form");',\yii\web\View::POS_READY,'z-main');
if ($tmp=\yii::$app->request->get('openField')){
    $this->registerJs('$(\'#zakaz-nav a[href="#fileinput-mian"]\').tab("show");',\yii\web\View::POS_READY,'z-main-change-tab');
}
$this->beginContent('@app/views/layouts/zakazLOUT.php');
$isDes=$this->context->role==='desiner'||$isDesiner;
//$forOpt=[
//    'id'=>'zakaz-form',
//];

if (!isset($header)) $header='Добавляем заказ';
$this->title=$header;
$this->params['breadcrumbs'][] = ['label' =>'Заказы', 'url' => $this->context->zakazBackUrlOption($listBack)];
if ($model->isNewRecord){
    $this->params['breadcrumbs'][] = 'Новый заказ';
}else{
    $this->params['breadcrumbs'][] = ['label'=>'Заказ №'.$model->id,'url'=>$this->context->defaultUrlOption('details',['id'=>$model->id])];
}
if (!$isDes){
    $button=Html::submitButton(!isset($submitText)?($model->isNewRecord ? 'Добавить' : 'Сохранить'):$submitText, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','disabled'=>true]);
}else{
    $button=Html::tag('a','Назад',['class'=>'btn btn-primary','href'=>Url::to($this->context->zakazBackUrlOption($listBack))]);
}
$form = ActiveForm::begin(
    [
        'id'=>'zakaz-form',
        'action'=>$this->context->zakazBackUrlOption(!isset($actionId)?$this->context->action->id:$actionId,['id'=>$model->id]),
        'options' => ['enctype' => 'multipart/form-data'],
//        'enableAjaxValidation'=>true,
//        'ajaxDataType'=>'html',
        'validationUrl'=>  Url::to(['ajaxvalidate'])
    ]);
Panel::begin([
    //'options'=>['class'=>['panel-info']],
    'color'=>Panel::color_info,
    'panelHeader'=>Html::tag('h2',$header),
    'panelFooter'=>!\Yii::$app->request->isAjax?$button:''
    ]);
//echo Html::tag('h3','Скопировать файлы заказа '.Html::checkbox('copyFromOld',true),['title'=>'Количество файлов: '.$copyFiles['totalCount'].'шт. размером: '.yii::$app->formatter->asShortSize($copyFiles['totalSize'],2)]);
?>
<?php if (isset($copyFiles)):
        if($copyFiles['totalCount']):
    ?>
<div class="row copyFileInfo">
    <div class="col-sm-4 col-xs-4">
        <div class="input-group" title="<?='Количество файлов: '.$copyFiles['totalCount'].'шт. размером: '.yii::$app->formatter->asShortSize($copyFiles['totalSize'],2)?>">
            <span class="input-group-addon">
              <?=Html::checkbox('copyFromOld',true)?>
            </span>
            <?=Html::hiddenInput('parentZakazId',$parentZakazId)?>
            <input type="text" class="form-control" value="Скопировать файлы заказа" readonly="readonly">
        </div>
    </div>
</div>
<?php endif;
    endif;?>

<?= Tabs::widget([
    //'options'=>['class'=>'zakaz-tab'],
    'id'=>'zakaz-nav',
    'items' => [
        [
            'label' => 'Заказ',
            'content' => $this->render('z-main',['form'=>&$form,'model'=>$model]),
            'active' => !$isDes,
            'options' => ['id' => 'z-main'],
            'visible'=>!$isDes
        ],
        [
            'label' => 'Исполнитель',
            'content' => $this->render('z-executor',['form'=>&$form,'model'=>$model,]),
            'options' => ['id' => 'executorID'],
            'visible'=>!$isDes
        ],
        [
            'label' => 'Материал',
            'content' => $this->render('z-specification',['form'=>&$form,'model'=>$model]),
            //'headerOptions' => [...],
            'options' => ['id' => 'specification'],
            'visible'=>!$isDes
        ],
        [
            'label' => 'Параметры изделия',
            'content' => $this->render('z-parameters',['form'=>&$form,'model'=>$model,'isDes'=>$isDes]),
            //'headerOptions' => [...],
            'options' => ['id' => 'parameters'],
        ],
        [
            'label' => 'Оплата и отгрузки',
            'content' => $this->render('z-otgruzki',['form'=>&$form,'model'=>$model]),
            //'headerOptions' => [...],
            'options' => ['id' => 'otgruzki-mian'],
            'visible'=>!$isDes
        ],
        [
            'label' => 'Доставка',
            'content' => $this->render('z-dostavka',['form'=>&$form,'model'=>$model]),
            //'headerOptions' => [...],
            'options' => ['id' => 'dostavka-mian'],
            'visible'=>!$isDes
        ],
        [
            'label' => 'Файлы заказчика',
            'content' => $this->render('z-fileInput',['form'=>&$form,'model'=>$model,'isDes'=>$isDes]),
            //'headerOptions' => [...],
            'options' => ['id' => 'fileinput-mian'],
            'active'=>$isDes
        ],
        [
            'label' => 'Файлы дизайнера',
            'content' => $this->render('z-fileOutput',['form'=>&$form,'model'=>$model]),
            //'headerOptions' => [...],
            'options' => ['id' => 'fileoutput-mian'],
        ],
    ],
])?>

<?php Panel::end();
      ActiveForm::end(); 
?>
<?php Modal::begin([
    'header' => '<h2>Hello world</h2>',
    'id'=>'askMoadal',
    'headerOptions'=>['id'=>'askMoadalHeader'],
    //'size'=>Modal::SIZE_SMALL,
    'footer'=>Html::button('Закрыть',['class'=>'btn btn-default','data-dismiss'=>'modal']).Html::button('Сохранить',['id'=>'askMoadalSave','class'=>'btn btn-primary'])
    //'toggleButton' => ['label' => 'click me'],
]);
    echo ActiveForm::widget(['id'=>'askMoadal-form','action'=>'#','options'=>['class'=>'form-horizontal']]);
    Modal::end();
?>
<?php $this->endContent(); ?>