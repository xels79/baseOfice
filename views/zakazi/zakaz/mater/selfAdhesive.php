<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\VarDumper;
$this->beginContent('@app/views/layouts/zakazMaterialLOU.php');
$lbl='col-md-4 control-label';
$pricem2_disabled=isset($values['supplierType'])?((int)$values['supplierType']!==2):true;
if (!$pricem2_disabled){
    if (isset($values['priceppc'])){
        $pricem2_disabled=$values['priceppc']!=0;
    }
}
?>

<div class="col">
    <div class="form-group supplierType">
            <div class="input-group">
                <?=Html::radioList('Zakaz[materialDetails][value][supplierType]',isset($values['supplierType'])?$values['supplierType']:0,['Заказчик','Исполнитель','Наш'],['id'=>'paperName-supplierType'])?>
            </div>
    </div>

    <?php
        foreach(array_keys($model->fieldList) as $fName){
            echo Html::tag('div',$model->createLabel($fName,['class'=>$lbl]).$model->creatField($fName,'Zakaz[materialDetails][value][field]',$values),['class'=>'form-group']);
        }
    ?>
    <div class="form-group">
            <?=Html::tag('label',$cenLabel,['class'=>$lbl])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value][priceppc]',isset($values['priceppc'])?$values['priceppc']:0,['class'=>'form-control','disabled'=>isset($values['supplierType'])?((int)$values['supplierType']!==2):true, 'id'=>'paperName-priceppc'])?>
            </div>
    </div>
    <div class="form-group">
            <?=Html::tag('label',$cenLabelM2,['class'=>$lbl])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value][pricem2]',isset($values['pricem2'])?$values['pricem2']:0,['class'=>'form-control','disabled'=>$pricem2_disabled, 'id'=>'paperName-pricem2'])?>
            </div>
    </div>
    <?php
        if (isset($dopField)){
            foreach($dopField as $key=>$val){
                $dsb=false;
                if (isset($val['checkBox'])){
                    if (isset($values[$key])){
                        if (!$values[$key]) $dsb=true;
                    }else{
                        $dsb=true;
                    }
                }
                echo Html::tag('div',
                        (isset($val['label'])?Html::tag('label',$val['label'],['class'=>$lbl]):'').
                        Html::tag('div',
                        (Html::textInput('Zakaz[materialDetails][value]['.$key.']',isset($values[$key])?$values[$key]:(isset($dopField[$key]['default'])?$dopField[$key]['default']:null),['class'=>'form-control','disabled'=>$dsb])).
                            (isset($val['checkBox'])?Html::tag('spam',Html::checkbox('',false,['id'=>$key.'_check']),['class'=>'input-group-addon']):''),
                        ['class'=>'input-group']),
                        ['class'=>'form-group']);
            }
        }
    ?>
    <div class="form-group">
        <label class="<?=$lbl?>"><?=$countName?></label>
        <div class="input-group">
            <?=Html::textInput('Zakaz[materialDetails][value][count]',isset($values['count'])?$values['count']:null,['class'=>'form-control','id'=>'paperName-count'])?>
        </div>
    </div>
    <div class="form-group">
            <?=Html::tag('label','Итог:',['class'=>$lbl.' control-label'])?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value][summ]',isset($values['summ'])?$values['summ']:'0.00',['class'=>'form-control','readonly'=>true, 'id'=>'paperName-summ'])?>
            </div>
    </div>
<?php 
    if (isset($values['payed'])) 
        echo Html::hiddenInput('Zakaz[materialDetails][value][payed]',$values['payed']);
    if (isset($values['dateOfOrder'])){
        if ($values['dateOfOrder']&&$this->context->role==='admin'){
            echo Html::beginTag('div',['class'=>'form-group']);
            echo Html::label('',null,['class'=>$lbl.' control-label']);
            echo Html::beginTag('div',['class'=>'input-group']);
            echo Html::button('Сброс. мат. заказан',[
                'id'=>'dateOfOrder',
                'class'=>'btn btn-danger',
                'title'=>Yii::$app->formatter->asDate($values['dateOfOrder']),
                'data'=>[
                    'remove'=>'#materialDetails-dateOfOrder',
                    'text'=>'заказа'
                ],
            ]);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
        echo Html::hiddenInput('Zakaz[materialDetails][value][dateOfOrder]',$values['dateOfOrder'],[
            'id'=>'materialDetails-dateOfOrder',
            'style'=>['display'=>'none']
        ]);
    }
    if (isset($values['dateOfGet'])){
        if ($values['dateOfGet']&&$this->context->role==='admin'){
            echo Html::beginTag('div',['class'=>'form-group']);
            echo Html::label('',null,['class'=>$lbl.' control-label']);
            echo Html::beginTag('div',['class'=>'input-group']);
            echo Html::button('Сброс. мат. получ',[
                'id'=>'dateOfGet',
                'class'=>'btn btn-danger',
                'title'=>Yii::$app->formatter->asDate($values['dateOfGet']),
                'data'=>[
                    'remove'=>'#materialDetails-dateOfGet',
                    'text'=>'получения'
                ]
            ]);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
        echo Html::hiddenInput('Zakaz[materialDetails][value][dateOfGet]',$values['dateOfGet'],[
            'id'=>'materialDetails-dateOfGet',
            'style'=>['display'=>'none']
        ]);
    }
?>

</div>
<?php
$this->endContent();