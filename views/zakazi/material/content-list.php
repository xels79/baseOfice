<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\Panel;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
$modalName='AddChange';
if ($model){
    $this->registerJs ( 'materialInit("'.$modalName.'");',yii\web\View::POS_READY );
    $tmp=$model->listVal;
    $attrL=$model->attributeLabels();
    foreach (array_keys($tmp)as $key){
        $item=[];
        if ($key!==''){
        foreach ($tmp[$key]as $it){
            $item[]=[
                'label'=>$it,
                'control'=>[
                    'changeButton'=>true,
                    'removeButton'=>false
                ]
                //'id'=>$tblName.'['.$key.']'
            ];
        }?>
        <div class="row">
        <?php
        Panel::begin([
            'panelHeader'=>['content'=>$attrL[$key],'options'=>['class'=>'active-label']],
        ]);?>
        <?php 
        ?>
        <?= app\widgets\ActiveListBox::widget([
            'items'=>$item,
            'id'=>$tblName.'['.$key.']',
            'formToSaveId'=>'firmForm',
            'pageName'=>'Назад',
            'changeFunction'=>'materialChange',
            'removeFunction'=>'materialRemove',
            'modalTarget'=>$modalName,
            //'updateThisAction'=>\yii\helpers\Url::to(['admin/firms/ajaxupdaterequest','id'=>'$modelId']),
            'addKeyId'=>'ManagerAddManager',
            
            //'removeAction'=>\yii\helpers\Url::to(['admin/manager/remove','id'=>'$modelId']),
        ]);?>
        <?php Modal::begin([
            'id'=>$modalName.'_'.$key,
            'size'=>Modal::SIZE_SMALL,
            'header' => '<h2>Добавить</h2>',
            'toggleButton' => [
                'label' => app\widgets\Glyphicon\AddGlyphicon::widget(),
                'id'=>$modalName.'_'.$key.'_Add',
                'firm-id'=>$model->id,
                //'url'=>yii\helpers\Url::to(['admin/manager/ajaxadd']),
                'class' => 'btn btn-success btn-sm',
                'title'=>'Добавить',
                'encode'=>false
                ],
            'footer'=>\yii\bootstrap\ButtonGroup::widget([
                'buttons'=>[
                    [
                        'id'=>$modalName.'_'.$key.'_AddCreate',
                        'options' => [
                            'class' => 'btn btn-success',
                            'data-target'=>'#'.$modalName,
                            'url'=>Url::to(['admin/manager/ajaxadd']),
                            'role'=>'dialogSave'
                            ],
                        'label'=>'Сохранить',
                    ],
                    [
                        'id'=>$modalName.'_'.$key.'_AddCansel',
                        'options' => ['class' => 'btn btn-default','data-target'=>'#'.$modalName,'role'=>'dialogCancel'],
                        'label'=>'Отменить',

                    ]
                ]
            ])
        ]);
        echo Html::textInput($key);
        Modal::end();?>
        <?php Panel::end();?>
        </div>
    <?php
        }
    }
}

