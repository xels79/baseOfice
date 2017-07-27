<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
?>
<div class="zakaz-tab">
    <div class="form-horizontal">
        <div class="form-group col-lg-3">
            <label class="col-sm-4 control-label">Стоимость:</label>
            <div class="col-lg-5">
            <?=Html::textInput('',0,['class'=>'form-control','readonly'=>true,'id'=>'dostavka_summ','role'=>'totalCoast'])?>
            </div>
        </div>
    <div class="row">
        <table id="zakaz-contractors-tbl" class="table table-hover">
            <tr>
                <th>Куда</th>
                <th class="standart">Фирма</th>
                <th class="standart">Сумма</th>
                <th class="standart">Транспорт</th>
                <th class="standart">Дата</th>
                <th class="standart">Адрес</th>
                <th></th>
            </tr>
            <tr role="toAdd" style="display:none;">
                <td>
                    <?=//Html::dropDownList('manExexSel',null,[],['id'=>'item-manExexSel','style'=>'display:none;'])
                       Html::hiddenInput('dostavkaFirm',null,['style'=>'display:none;'])
                    ?>
                    <span>Фирма Куда</span></td>
                <td class="standart">
                    <?=//Html::dropDownList('manExexSel',null,[],['id'=>'item-manExexSel','style'=>'display:none;'])
                       Html::hiddenInput('dostavkaCoast',null,['style'=>'display:none;'])
                    ?>
                    
                    <div class="input-group input-group-sm">
                        <?=Html::textInput('dostavkaCoast',null,['placeholder'=>'Выберите','class'=>'form-control'])?>
                        <div class="input-group-btn">
                            <?=Html::textInput('dostavkaCoast2',0,['class'=>''])?>
                        </div>
                    </div>
                </td>
                <td class="standart">
                    <?=app\widgets\ActiveDropdown::widget([
                        'items'=>$model->metodIspolneny,
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

                <td><?= app\widgets\Glyphicon\RemoveGlyphiconButton::widget(['options'=>['url'=>'#']])?></td>
            </tr>
        </table>
    </div>

    </div>
</div>