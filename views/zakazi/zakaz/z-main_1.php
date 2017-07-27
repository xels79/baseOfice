<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\ActiveDropdown;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;
use app\widgets\JSRegister;
use yii\helpers\Url;
use app\models\Zakaz;

$menuId='custNW';
$col2='col-lg-3 col-md-3 col-sm-4 col-xs-2';
$col3='col-lg-3 col-md-2 col-sm-3 col-xs-2';
$ajaxupdaterequest=Url::to(['ajaxupdaterequest']);
?>
<?php JSRegister::begin([
    'key' => 'zakazAddMain',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    new zakazDialogs('<?=$menuId?>','<?=$ajaxupdaterequest?>');
</script>
<?php JSRegister::end();?>
<div class="zakaz-tab">
    <div class="row">
        <div class="<?=$col3 ?>">
            <?= $form->field($model,'dateOfAdmission')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options'=>['class'=>'form-control'],
            
             ])
             ?>
        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'managerId',['options'=>['style'=>'display:none;']])->hiddenInput()->label(false) ?>
            <?= $form->field($model,'oManager')->textInput(['readonly'=>true,'title'=>'Заказ менеджера: '.$model->oManager]) ?>
        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'customerName')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId,
                    'items'=>$model->firmsListNew,
                    'selected'=>$model->customerName,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true,
                    //'noScript'=>$model->isNewRecord,
                    'infoTag'=>'input',
                    //'options'=>['class'=>'col-xs-3'],
                    'placeholder'=>'Выберите...'
                    //'buttonTag'=>'a'
                ]) ?>
        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'customerManager')->widget(ActiveDropdown::className(),[
                    'id'=>'custMan',
                    'menuId'=>$menuId.'_Man',
                    'items'=>$model->firmIdByManager?$model->getManagerList2($model->firmIdByManager):[],
                    'selected'=>$model->customerManager,
                    'options'=>['class'=>'zakaz-bt-group pull-left'],
                    'autoDisable'=>true,
                    'noScript'=>$model->isNewRecord,
                    'infoTag'=>'input',
                    'placeholder'=>'Выберите...',
                    //'labelId'=>true
                    //'afterClickFunction'=>'Zakaz.DDValidate'
                ]) ?>
        </div>

    </div>
    <div class="row">
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'orderType')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId.'_ordT',
                    'items'=>$model->zakazTypes,
                    'selected'=>$model->orderType,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true,
                    'afterClickFunction'=>'Zakaz.orderType'
                ]) ?>

        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'name')->textInput() ?>
        </div>
        <div class="<?=$col3 ?>">
            <?= $form->field($model,'numberOfCopies')->textInput() ?>
        </div>
        <div class="<?=$col3 ?>">
            <?= $form->field($model,'deadline')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options'=>['class'=>'form-control'],
            //'containerOptions'=>'dt-pick'

             ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'methodOfExecution')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId.'_MOET',
                    'items'=>$model->metodIspolneny,
                    'selected'=>$model->methodOfExecution,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true
                ]) ?>
        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'stage')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId.'_stageT',
                    'selected'=>!$model->isNewRecord?$model->stage:0,
                    'items'=>Zakaz::_stages,//$model->zakazTypes,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true
                ]) ?>

        </div>
        <div class="<?=$col2 ?>" style="display:none;">
            <?= $form->field($model,'payment')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId.'_paymentT',
                    'selected'=>!$model->isNewRecord?$model->payment:0,
                    'items'=>Zakaz::_payments,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true
                ])->label(false)->hiddenInput() ?>

        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'special_attention')->textInput() ?>
        </div>        

    </div>
    <div class="row">
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'totalCost')->textInput() ?>
        </div>   
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'paymentMethod')->widget(ActiveDropdown::className(),[
                    'menuId'=>$menuId.'_paymentMethodT',
                    'selected'=>!$model->isNewRecord?$model->paymentMethod:0,
                    'items'=>['№ счета','Договорная','В/з'],//$model->zakazTypes,
                    'options'=>['class'=>'zakaz-bt-group'],
                    'autoDisable'=>true,
                    'afterClickFunction'=>'Zakaz.paymentMethodClick'
                ]) ?>

        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'accountNumber')->textInput(['disabled'=>!$model->paymentMethod==0]) ?>
        </div>   
    </div>
</div>
