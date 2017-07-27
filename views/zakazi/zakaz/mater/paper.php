<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * $supplier->поставщики [id=>name]
 * $selectedSupplier - выбранный поставщик
 */
use yii\helpers\Url;
use yii\helpers\Html;
if (!isset($paperNum)) $paperNum=1;
$ajaxupdaterequest=Url::to(['ajaxupdaterequest']);
$this->beginContent('@app/views/layouts/zakazMaterialLOU.php',[
    'supplier'=>&$supplier,
    'selectedSupplier'=>&$selectedSupplier,
    'paperNum'=>$paperNum
]);
//if ($selectedSupplier){
$lbl='col-md-4';
$cntr='';
//       \yii\helpers\VarDumper::dump($model->dropDownList('description'),10,true);
//       \yii::$app->end();
?>

<div class="col" papernum="<?=$paperNum?>">
    <div class="form-group supplierType">
            <div class="input-group">
                <?=Html::radioList('Zakaz[materialDetails][value]['.$paperNum.'][supplierType]',isset($values['supplierType'])?$values['supplierType']:0,\yii::t('app','supplierTypeArray'),['id'=>'paperName-supplierType'.$paperNum])?>
            </div>
    </div>
    <div class="form-group" title="Описание">
        <?=!$isSecond?Html::tag('label','Описание:',['for'=>'paperName-description','class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            'selected'=>isset($values['description'])?$values['description']:false,
            'formControlID'=>'materialDetails-description'.$paperNum,
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][description]',
            'items'=>$model->dropDownList('description'),
            'menuId'=>'description'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            'noScript'=>true,
            //'selected'=>$selectedSupplier?$selectedSupplier:false,
            'options'=>['class'=>$cntr,'zmControl'=>'control']
            //'clickFunction'=>'zMat.supplierClick'
        ])?>
    </div>

    <div class="form-group" title="Поставщик">
        <?=!$isSecond?Html::tag('label','Поставщик:',['class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            'selected'=>isset($values['supplier'])?(int)$values['supplier']:false,
            'formControlID'=>'materialDetails-selSupplier'.$paperNum,
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][supplier]',
            'items'=>$supplier,
            'menuId'=>'selSupplier'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            //'selected'=>$selectedSupplier?$selectedSupplier:false,
            'exactValue'=>true,
            'options'=>['class'=>$cntr,'zmControl'=>'control']
            //'clickFunction'=>'zMat.supplierClick'
        ])?>
    </div>

    <div class="form-group" title="Название">
        <?=!$isSecond?Html::tag('label','Название:',['class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            //'items'=>$supplier,
            'selected'=>isset($values['paperName'])?(int)$values['paperName']:false,
            'menuId'=>'paperName'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            //materialDetails
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][paperName]',
            'formControlID'=>'materialDetails-paperName'.$paperNum,
            'items'=>$model->dropDownList('paperName'),
            'options'=>['class'=>$cntr],
            //'debug'=>true
        ])?>
    </div>
    <div class="form-group" title="Цвет">
        <?=!$isSecond?Html::tag('label','Цвет:',['class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            'selected'=>isset($values['pcolors'])?$values['pcolors']:false,
            'menuId'=>'pcolors'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            'likeOnBeforListUpdateGetParam'=>'zMat.dopListParam',
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][pcolors]',
            'formControlID'=>'materialDetails-pcolors'.$paperNum,
            'items'=>$model->dropDownList('pcolors',isset($values['paperName'])?app\models\PaperName::findName((int)$values['paperName']):null),
            'options'=>['class'=>$cntr]
        ])?>
    </div>
    <div class="form-group" title="Плотность">
        <?=!$isSecond?Html::tag('label','Плотность:',['class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            'selected'=>isset($values['density'])?$values['density']:false,
            'menuId'=>'density'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            //materialDetails
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][density]',
            'formControlID'=>'materialDetails-density'.$paperNum,
            'items'=>$model->dropDownList('density'),
            'options'=>['class'=>$cntr]
        ])?>
    </div>
    <div class="form-group" title="Формат">
        <?=!$isSecond?Html::tag('label','Формат:',['class'=>$lbl.' control-label']):''?>
        <?=app\widgets\ActiveDropdown::widget([
            'selected'=>isset($values['sizes'])?$values['sizes']:false,
            'menuId'=>'format'.$paperNum,
            'infoTag'=>'input',
            'placeholder'=>'Выберите или введите',
            'likeFilterAjaxUrl'=>$ajaxupdaterequest,
            'likeRequestName'=>'material',
            'likeParamAddToLink'=>true,
            //materialDetails
            'formControlName'=>'Zakaz[materialDetails][value]['.$paperNum.'][field][sizes]',
            'formControlID'=>'materialDetails-format'.$paperNum,
            'likeFilterVarName'=>'formatLike',
            'items'=>$model->dropDownList('sizes'),
            'options'=>['class'=>$cntr]
        ])?>
    </div>
    <div class="form-group" title="Количество">
            <?=!$isSecond?Html::tag('label','Количество:',['for'=>'paperName-count', 'class'=>$lbl.' control-label']):''?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value]['.$paperNum.'][count]',isset($values['count'])?$values['count']:null,['class'=>'form-control '.$cntr,'id'=>'paperName-count'.$paperNum])?>
            </div>
    </div>
    <div class="form-group">
            <?=!$isSecond?Html::tag('label','Цена_листа:',['for'=>'paperName-priceppc', 'class'=>$lbl.' control-label']):''?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value]['.$paperNum.'][priceppc]',isset($values['priceppc'])?$values['priceppc']:'0',['class'=>'form-control '.$cntr,'disabled'=>isset($values['supplierType'])?((int)$values['supplierType']!==2):true, 'id'=>'paperName-priceppc'.$paperNum])?>
            </div>
    </div>
    <div class="form-group">
            <?=!$isSecond?Html::tag('label','Итог:',['for'=>'paperName-summ', 'class'=>$lbl.' control-label']):''?>
            <div class="input-group">
                <?=Html::textInput('Zakaz[materialDetails][value]['.$paperNum.'][summ]',isset($values['summ'])?$values['summ']:'0.00',['class'=>'form-control '.$cntr,'readonly'=>true, 'id'=>'paperName-summ'.$paperNum])?>
            </div>
    </div>
<?php 
    if (isset($values['payed']))
        echo Html::hiddenInput('Zakaz[materialDetails][value]['.$paperNum.'][payed]',$values['payed']);
    if (isset($values['dateOfOrder'])){
        if ($values['dateOfOrder']&&$this->context->role==='admin'){
            echo Html::beginTag('div',['class'=>'form-group']);
            echo !$isSecond?Html::label('',null,['class'=>$lbl.' control-label']):'';
            echo Html::beginTag('div',['class'=>'input-group']);
            echo Html::button('Сброс. мат. заказан',[
                'id'=>'dateOfOrder'.$paperNum,
                'class'=>'btn btn-danger',
                'title'=>Yii::$app->formatter->asDate($values['dateOfOrder']),
                'data'=>[
                    'remove'=>'#materialDetails-dateOfOrder'.$paperNum,
                    'text'=>'заказа'
                ],
            ]);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
        echo Html::hiddenInput('Zakaz[materialDetails][value]['.$paperNum.'][dateOfOrder]',$values['dateOfOrder'],[
            'id'=>'materialDetails-dateOfOrder'.$paperNum,
            'style'=>['display'=>'none']
        ]);
    }
    if (isset($values['dateOfGet'])){
        if ($values['dateOfGet']&&$this->context->role==='admin'){
            echo Html::beginTag('div',['class'=>'form-group']);
            echo !$isSecond?Html::label('',null,['class'=>$lbl.' control-label']):'';
            echo Html::beginTag('div',['class'=>'input-group']);
            echo Html::button('Сброс. мат. получ',[
                'id'=>'dateOfGet'.$paperNum,
                'class'=>'btn btn-danger',
                'title'=>Yii::$app->formatter->asDate($values['dateOfGet']),
                'data'=>[
                    'remove'=>'#materialDetails-dateOfGet'.$paperNum,
                    'text'=>'получения'
                ]
            ]);
            echo Html::endTag('div');
            echo Html::endTag('div');
        }
        echo Html::hiddenInput('Zakaz[materialDetails][value]['.$paperNum.'][dateOfGet]',$values['dateOfGet'],[
            'id'=>'materialDetails-dateOfGet'.$paperNum,
            'style'=>['display'=>'none']
        ]);
    }
?>

</div>
    <div class="btn-group btn-group-xs">
        <?=$paperNum<4?app\widgets\Glyphicon\AddGlyphiconButton::widget(['id'=>'addPaper']):'';?>
        <?=$paperNum<5&&$paperNum>1?app\widgets\Glyphicon\RemoveGlyphiconButton::widget(['id'=>'removePaper']):'';?>
    </div>

    <?php 
//}
$this->endContent(); 
?>