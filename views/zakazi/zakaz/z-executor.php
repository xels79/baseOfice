<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\widgets\ActiveDropdown;
use app\widgets\ActiveListBox;
use app\widgets\Panel;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;
use yii\helpers\Json;
$menuId='execNW';
$col2='col-lg-2 col-md-5 col-sm-6';
$col3='col-lg-4 col-md-5 col-sm-6';
$modalName='execModal';
$execCoast=$model->execCoast;
//       \yii\helpers\VarDumper::dump($execCoast,10,true);
//        \yii::$app->end();
////{id:a.attr('value'),label:a.text(),selectId:Obj.selectId,element:a};
$opt=[];
if (isset($execCoast['value']))
    if (is_array($execCoast['value'])){
        foreach($execCoast['value'] as $rowTmp){
            if ($model->isNewRecord&&isset($rowTmp['payed'])) unset($rowTmp['payed']);
            //yii\helpers\VarDumper::dump($rowTmp,10,true);echo '<br>';
            $opt[]=[
                'id'=>$rowTmp['idFirm'],
                'label'=>$model->firmNameById($rowTmp['idFirm']),
                'values'=>$rowTmp
                ];
        }
        //Yii::$app->end();
    }
if (count($opt)){
    $this->registerJs('Zakaz.showExecutersToEdit('.Json::encode($opt).')',\yii\web\View::POS_READY, '_execCoastReInit');
}
?>
<div class="zakaz-tab">
    <div class="form-horizontal">
        <div class="form-group col-lg-3">
            <label class="col-sm-4 control-label">Стоимость:</label>
            <div class="col-lg-5">
            <?=Html::textInput('Zakaz[execCoast][summ]',$execCoast['summ'],['class'=>'form-control','readonly'=>true,'id'=>'execCoast_summ','role'=>'totalCoast'])?>
            </div>
        </div>
        <div class="form-group col-lg-3">
            <label class="col-sm-4 control-label">Выплаты:</label>
            <div class="col-lg-5">
            <?=Html::textInput('Zakaz[execCoast][payments]',$execCoast['payments'],['class'=>'form-control','readonly'=>true,'id'=>'execCoast_payments'])?>
            </div>
        </div>
        <div class="form-group col-lg-3">
            <label class="col-sm-4 control-label">Прибыль:</label>
            <div class="col-lg-5">
            <?=Html::textInput('Zakaz[execCoast][profit]',$execCoast['profit'],['class'=>'form-control','readonly'=>true,'id'=>'execCoast_profit'])?>
            </div>
        </div>
        <div class="form-group col-lg-3">
            <label class="col-sm-5 control-label">Сверхприбыль:</label>
            <div class="col-lg-5">
            <?=Html::textInput('Zakaz[execCoast][superprofit]',$execCoast['superprofit'],['class'=>'form-control','readonly'=>true,'id'=>'execCoast_superprofit'])?>
            </div>
        </div>
    </div>
    <div class="row">
        <table id="zakaz-contractors-tbl" class="table table-hover">
            <tr>
                <th></th>
                <th>Фирма исполнители</th>
                <th class="standart">Ответственный</th>
                <th class="standart">Вид работы</th>
                <th class="standart">Стоимость</th>
                <th class="standart">Выплаты</th>
                <th class="standart">Прибыль</th>
                <th class="standart">Сверхприбыль</th>
                <th></th>
                <th class="sr-only"></th>
            </tr>
            <tr role="toAdd" style="display:none;">
                <td>Дополнительный</td>
                <td>
                    <?=//Html::dropDownList('manExexSel',null,[],['id'=>'item-manExexSel','style'=>'display:none;'])
                       Html::hiddenInput('firmExexSel',null,['style'=>'display:none;'])
                    ?>
                    <span>Фирма</span></td>
                <td class="standart">
                    <?=//Html::dropDownList('manExexSel',null,[],['id'=>'item-manExexSel','style'=>'display:none;'])
                       Html::hiddenInput('manExexSel',null,['style'=>'display:none;'])
                    ?>
                    
                    <div class="input-group input-group-sm">
                        <?=Html::textInput('manExexSel2',null,['placeholder'=>'Выберите','class'=>'form-control'])?>
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
                <td class="sr-only">Html::hiddenInput('payed',null,['style'=>'display:none;'])?></td>
            </tr>
        </table>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-5 col-sm-6">
            <div class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><?=\app\widgets\Glyphicon\AddGlyphicon::widget()?></a>
                <?php
                    echo Dropdown::widget([
                        'id'=>'zakaz-contractors-sel',
                        'items' => $model->executerList,
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>