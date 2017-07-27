<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\widgets\ActiveForm;
use app\widgets\ActiveDropdown;
use yii\helpers\Url;

$menuId='custNW';
$form = ActiveForm::begin(
    [
        'id'=>'zakaz-form',
        'options' => ['enctype' => 'multipart/form-data']
    ]);
?>
<?= $form->field($model,'customerName')->widget(ActiveDropdown::className(),[
        'menuId'=>$menuId,
        'items'=>$model->firmsListNew,
        'selected'=>$model->customerName,
        'options'=>['class'=>'zakaz-bt-group'],
        'autoDisable'=>true,
        //'noScript'=>$model->isNewRecord,
        'infoTag'=>'input',
        //'options'=>['class'=>'col-xs-3'],
        'placeholder'=>'Выберите или введите',
        'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
        'likeRequestName'=>'mainLike',
        'likeParamAddToLink'=>true,
    ]) ?>

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
            'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
            'likeRequestName'=>'mainLike',
            'likeParamAddToLink'=>true,
        ]) ?>
        <?= $form->field($model,'orderType')->widget(ActiveDropdown::className(),[
                'menuId'=>$menuId.'_ordT',
                'items'=>$model->zakazTypes,
                'selected'=>$model->orderType,
                'options'=>['class'=>'zakaz-bt-group'],
                'autoDisable'=>true,
                'afterClickFunction'=>'Zakaz.orderType',
                'likeFilterAjaxUrl'=>Url::to(['ajaxupdaterequest']),
                'likeRequestName'=>'mainLike',
                'likeParamAddToLink'=>true,
                'exactValue'=>true
            ]) ?>
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
               'autoDisable'=>true
            ]) ?>

<?php ActiveForm::end();?>