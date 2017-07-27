<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Zakaz;
$lbl='col-md-6';
$cntr='col-lg-8';
$massKey=  array_keys(Zakaz::_mass);
$param=$model->param;
$pPrint=ArrayHelper::getValue($param,'post',[]);
$ppBackVal=ArrayHelper::getValue($param,'pantonBack',[]);
$ppFaceVal=ArrayHelper::getValue($param,'pantonFace',[]);
$ppCount=$model->faceTypeCount()>$model->backTypeCount()?$model->faceTypeCount():$model->backTypeCount();
?>
<div class="form-horizontal pull-left">
    <div class="row">
        <div class="form-group col-md-6">
            <?=Html::tag('label','Заказ:',['for'=>'product-name', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                 <?=Html::textInput('',$model->orderType?$model->zakazTypes[$model->orderType]:'',['class'=>'form-control '.$cntr, 'readonly'=>true, 'id'=>'product-name'])?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <?=Html::tag('label','Тираж:',['for'=>'product-quantity', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                 <?=Html::textInput('',$model->numberOfCopies?$model->numberOfCopies:0,['class'=>'form-control '.$cntr, 'readonly'=>true, 'id'=>'product-quantity'])?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <?=Html::tag('label','Размер готового изделия:',['for'=>'paperName-summ', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[parameters][0]',ArrayHelper::getValue($param,0),['class'=>'form-control '.$cntr,'readonly'=>$isDes])?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::tag('label','Формат печатного блока:',['for'=>'paperName-summ', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[parameters][1]',ArrayHelper::getValue($param,1),['class'=>'form-control '.$cntr,'readonly'=>$isDes])?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::tag('label','Кол-во изделий в блоке:',['for'=>'paperName-summ', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[parameters][2]',ArrayHelper::getValue($param,2),['class'=>'form-control '.$cntr, 'id'=>'product-format','readonly'=>$isDes])?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::tag('label','Кол-во печатных блоков:',['for'=>'paperName-summ', 'class'=>$lbl.' control-label'])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[parameters][3]',ArrayHelper::getValue($param,3),['class'=>'form-control '.$cntr, 'id'=>'product-format','readonly'=>$isDes])?>
            </div>
        </div>
    </div>
    <div class="row">
        <table class="table table-bordered parameters-color">
            <tr><th></th><th>Лицо</th><th>Оборот</th><th>Примечание:</th></tr>
            <tr>
                <th>Цветность:</th>
                <td><?=!$isDes?Html::dropDownList('Zakaz[parameters][faceTypeId]',ArrayHelper::getValue($param,'faceTypeId',null),Zakaz::_ppFace,['id'=>'faceType']):Html::textInput('Zakaz[parameters][faceTypeId]',ArrayHelper::getValue($param,'faceTypeId',null),['id'=>'faceType','readonly'=>true])?></td>
                <td><?=!$isDes?Html::dropDownList('Zakaz[parameters][backTypeId]',ArrayHelper::getValue($param,'backTypeId',null),Zakaz::_ppBack,['id'=>'backType']):Html::textInput('Zakaz[parameters][backTypeId]',ArrayHelper::getValue($param,'backTypeId',null),['id'=>'backType','readonly'=>true])?></td>
                <td <?=$ppCount?'rowspan="'.($ppCount+1).'"':''?>><?=Html::textarea('Zakaz[parameters][rem]',ArrayHelper::getValue($param,'rem'),['readonly'=>$isDes])?></td>
            </tr>
            <?php for($i=0;$i<$ppCount;$i++):?>
            <tr>
                <td></td>
                <td>
                    <?=Html::textInput('Zakaz[parameters][pantonFace]['.$i.']',ArrayHelper::getValue($ppFaceVal,$i),['readonly'=>$isDes])?>
                </td>
                <td>
                    <?=Html::textInput('Zakaz[parameters][pantonBack]['.$i.']',ArrayHelper::getValue($ppBackVal,$i),['readonly'=>$isDes])?>
                </td>
            </tr>
            <?php endfor; ?>
            <tr style="display:none;">
                <td></td><td><input type="text"/></td><td><input type="text"/></td>
            </tr>
        </table>
    </div>
    <div class="row">
        <table id='postpetchat' class="table table-bordered">
            <tr><th rowspan="<?=count($massKey)?>">Постпечатка:</th><td><?=Zakaz::_mass['offsetlak']?></td><td><?=Html::textInput('Zakaz[parameters][post]['.$massKey[0].']',ArrayHelper::getValue($pPrint,$massKey[0],null),['readonly'=>$isDes])?></td></tr>
            <?php
            //array_shift($mass);
            array_shift($massKey);
            foreach ($massKey as $id){
            ?>
            <tr><td><?=Zakaz::_mass[$id]?></td><td><?=Html::textInput('Zakaz[parameters][post]['.$id.']',ArrayHelper::getValue($pPrint,$id,null),['readonly'=>$isDes])?></td></tr>
            <?php }?>
        </table>
    </div>
</div>