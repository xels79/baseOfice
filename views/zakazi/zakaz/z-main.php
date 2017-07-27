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
$col2='col-xs-3';
$col3='col-xs-3';
$col4='col-xs-4';
$ajaxupdaterequest=Url::to(['ajaxupdaterequest']);
?>
<?php JSRegister::begin([
    'key' => 'zakazAddMain',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var zDialog=new zakazDialogs('<?=$menuId?>','<?=$ajaxupdaterequest?>');
</script>
<?php JSRegister::end();?>
<div class="zakaz-tab">
    <div class="row">
        <div class="<?=$col3 ?>">
            <?=$form->field($model,'dateOfAdmission',['options'=>['style'=>['display'=>'none']]])->hiddenInput()->label(false)?>
            <?=$form->field($model,'dateOfAdmissionFormated')->textInput(['readonly'=>true])?>
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
                    'infoTag'=>'input',
                    'placeholder'=>'Выберите или введите',
                    'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
                    'likeRequestName'=>'mainLike',
                    'likeParamAddToLink'=>true,
                    'likeOnAfterListUpdate'=>'zDialog.initListRespons',
                    'exactValue'=>true
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
                    'placeholder'=>'Выберите или введите',
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
                    'infoTag'=>'input',
                    'placeholder'=>'Выберите или введите',
                    'afterClickFunction'=>'Zakaz.orderType',
                    'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
                    'likeRequestName'=>'mainLike',
                    'likeParamAddToLink'=>true,
                    'exactValue'=>true,
                ]) ?>

        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'name')->textInput() ?>
        </div>
        <div class="<?=$col3 ?>">
            <?= $form->field($model,'numberOfCopies',['enableAjaxValidation'=>true])->textInput(['placeholder'=>'Возможный формат (число * число)']) ?>
        </div>
        <div class="<?=$col3 ?>">
            <?= $form->field($model,'deadline')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options'=>['class'=>'form-control'],
            'clientOptions'=>$model::_defDatePickerOption
            //'containerOptions'=>'dt-pick'

             ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-9">
            <div class="row">
                <div class="<?=$col4 ?>">
                    <?= $form->field($model,'methodOfExecution')->widget(ActiveDropdown::className(),[
                            'menuId'=>$menuId.'_MOET',
                            'items'=>$model->metodIspolneny,
                            'selected'=>$model->methodOfExecution,
                            'options'=>['class'=>'zakaz-bt-group'],
                            'infoTag'=>'input',
                            'placeholder'=>'Выберите или введите',
                            'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
                            'likeRequestName'=>'mainLike',
                            'likeParamAddToLink'=>true,
                            'autoDisable'=>true,
                            'exactValue'=>true
                        ]) ?>
                </div>
                <div class="<?=$col4 ?>">
                    <?= $form->field($model,'stage')->widget(ActiveDropdown::className(),[
                            'menuId'=>$menuId.'_stageT',
                            'selected'=>!$model->isNewRecord?$model->stage:0,
                            'items'=>Zakaz::_stages,//$model->zakazTypes,
                            'options'=>['class'=>'zakaz-bt-group'],
                            'autoDisable'=>true
                        ]) ?>

                </div>
            </div>
            <div class="row">
                <div class="<?=$col4 ?>">
                    <?= $form->field($model,'totalCost')->textInput() ?>
                </div>   
                <div class="<?=$col4 ?>">
                    <?= $form->field($model,'paymentMethod')->widget(ActiveDropdown::className(),[
                            'menuId'=>$menuId.'_paymentMethodT',
                            'selected'=>!$model->isNewRecord?$model->paymentMethod:0,
                            'items'=>$model::_paymentMethod,//$model->zakazTypes,
                            'options'=>['class'=>'zakaz-bt-group'],
                            'autoDisable'=>true,
                            'afterClickFunction'=>'Zakaz.paymentMethodClick'
                        ]) ?>

                </div>
                <div class="<?=$col4 ?>">
                    <?= $form->field($model,'accountNumber')->textInput(['disabled'=>!$model->paymentMethod==0]) ?>
                </div>   
            </div>
        </div>
        <div class="<?=$col2 ?>">
            <?= $form->field($model,'special_attention')->textarea(['rows'=>5]) ?>
        </div>        
    </div>
</div>
