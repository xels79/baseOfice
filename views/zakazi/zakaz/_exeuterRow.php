<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
$nPerf='Zakaz[execCoast][value]';
$idPerf='';
?>
<tr role="item" firmid="<?=$id?>">
    <td>Дополнительный</td>
    <td>Фирма</td>
    <td class="standart">
        <?=//Html::dropDownList('manExexSel',null,[],['id'=>'item-manExexSel','style'=>'display:none;'])
           Html::hiddenInput($nPerf.'['.$id.'][idManager]',$idManager,['style'=>'display:none;'])
        ?>

        <div class="input-group input-group-sm">
            <?=Html::textInput('manExexSel2',null,[
                'placeholder'=>'Выберите',
                'class'=>'form-control',
                'id'=>'manSel_'.$id.'LikeInput'
            ])?>
            <div class="input-group-btn">
                <div class="dropdown">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-sm"><span class="caret"></span></a>
                </div>
            </div>
        </div>
    </td>
    <td class="standart">
        <?=app\widgets\ActiveDropdown::widget([
            'items'=>$model->metodIspolneny,
            'formControlID'=>'wtInpexecCoast_2',
            'options'=>['class'=>'zakaz-bt-group input-group-sm']
        ])?>
    </td>
    <td class="standart">
        <div class="input-group input-group-sm">
            <?=Html::textInput('execCoast','0',['class'=>'form-control','role'=>'zaryad'])?>
            <span class="input-group-addon">руб.</span>
        </div>
    </td>
    <td class="standart">
        <div class="input-group input-group-sm">
            <?=Html::textInput('payments','0',['class'=>'form-control','role'=>'paymet'])?>
            <span class="input-group-addon">руб.</span>
        </div>
    </td>
    <td class="standart">
        <div class="input-group input-group-sm">
            <?=Html::textInput('profit','0',['class'=>'form-control','readonly'=>true])?>
            <span class="input-group-addon">руб.</span>
        </div>
    </td>
    <td class="standart">
        <div class="input-group input-group-sm">
            <?=Html::textInput('superprofit','0',['class'=>'form-control','readonly'=>true])?>
            <span class="input-group-addon">руб.</span>
        </div>
    </td>   

    <td><?= app\widgets\Glyphicon\RemoveGlyphiconButton::widget(['options'=>['url'=>'#']])?></td>
</tr>
