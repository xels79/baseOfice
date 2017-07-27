<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
//use app\widgets\AddresEdit;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Modal;
use app\widgets\ActiveListBox;
use app\widgets\Panel;
use app\widgets\JSRegister;
/* @var $this yii\web\View */
/* @var $model app\models\Firms */
/* @var $form yii\widgets\ActiveForm */
/*  Фирма   */

$modalName='Manager';
$this->registerJsFile($this->assetManager->publish('@app/controllers/admin/managerMan.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'managerMan');
//$this->registerJs('$.fn.mManInit("'.$modalName.'", function(){alert ("ok");})',\yii\web\View::POS_READY,'managerManInit');
?>
<?php JSRegister::begin([
    'key' => 'managerManInit',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
$.fn.mManInit('<?=$modalName?>', function(){alert ("ok");});
$("#firms-type").change(function(){
    if ($(this).val()==3){
        $("#prodT").css("display","block");
    }else{
        $("#prodT").css("display","none");
        $("#firms-productstypes").children(":first").attr("selected", "selected");
    }
});
</script>
<?php JSRegister::end(); ?>
    <?php $form = ActiveForm::begin([
        'id'=>'firmForm',
        'options'=>['class'=>'firms-form'],
        'action'=>$this->context->defaultBackUrlOption($this->context->action->id,['id'=>$model->id])
        ]); ?>
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <?= $form->field($model, 'name')->textinput() ?>
            <?php Panel::begin([
                'panelHeader'=>['content'=>'Род деятельности','options'=>['class'=>'active-label']],
            ]);?>
            <?=$form->field($model,'firmTypes')->checkboxList(['Заказчик','Исполнитель','Поставщик'],[
                'tag'=>false,
                'itemOptions'=>[
                    //'class'=>['form-control']
                ],
                'item'=>function ($index, $label, $name, $checked, $value){
                    yii::trace($checked,'firms');
                    $lbl=Html::tag('label',$label,[
                        'class'=>['form-control']
                    ]);
                    $spn=Html::tag('span',Html::checkbox($name!=''?($name):'',$checked,[
                        'value'=>$value,
                        ]),
                        [
                            'class'=>'input-group-addon'
                        ]);
                    return Html::tag('div',$lbl.$spn,[
                       'class'=>['input-group'] 
                    ]);
                },
            ])->label(false)?>
            <?php Panel::end();?>
            <?= $form->field($model, 'fone')->textinput()->hiddenInput()->label(false) ?>
            <?= app\widgets\ActiveFones::widget([
                'model'=>&$model,
                'attribute'=>'fone',
                'buttonAddOptions'=>['class'=>'btn btn-success','style'=>'margin:3px 5px;'],
                'header'=>'Контактные телефоны',
                ]) ?>

        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 ">
            <?= $form->field($model,'addres1')->textarea(['rows'=>4,])?>
            <?= $form->field($model,'addres2')->textarea(['rows'=>4,])?>
        <?php if (!$model->isNewRecord): ?>
            <?php
                Panel::begin([
                    'panelHeader'=>['content'=>'Менеджеры','options'=>['class'=>'active-label']],
                ]);
                echo $this->render('managerListBox',['managers'=>$model->managers,'modelId'=>$model->id]);
                
            ?>
            <?php
                //AddGlyphiconButton
                Modal::begin([
                    'id'=>$modalName,
                    'header' => '<h2>Добавить человека</h2>',
                    'toggleButton' => [
                        'label' => app\widgets\Glyphicon\AddGlyphicon::widget(),
                        'id'=>$modalName.'AddManager',
                        'firm-id'=>$model->id,
                        'url'=>yii\helpers\Url::to(['admin/manager/ajaxadd']),
                        'class' => 'btn btn-success btn-sm',
                        'title'=>'Добавить',
                        'encode'=>false
                        ],
                    'footer'=>\yii\bootstrap\ButtonGroup::widget([
                        'buttons'=>[
                            [
                                'id'=>$modalName.'AddManagerCreate',
                                'options' => [
                                    'class' => 'btn btn-success',
                                    'data-target'=>'#'.$modalName,
                                    'url'=>Url::to(['admin/manager/ajaxadd'])
                                    ],
                                'label'=>'Добавить',
                            ],
                            [
                                'id'=>$modalName.'AddManagerCansel',
                                'options' => ['class' => 'btn btn-default','data-target'=>'#'.$modalName],
                                'label'=>'Отменить',
                                
                            ]
                        ]
                    ])
                ]);

                echo 'Say hello...';

                Modal::end();
                Panel::end();
            ?>
        <?php endif?>

        </div>
    </div>

    
    <div class="row" >
    </div>
<div class="row">
    <div id="prodT" class="row" <?= $model->type!==3?' style="display:none"':'' ?>>
        <div class="form-group col-lg-11">
            <?= $form->field($model, 'productsTypes')->dropDownList($model->materalsNames
//                [
//                NULL=>'Не задано',
//                'paper'=>'Бумага',
//                ]
        )
            ?>
        </div>
    </div>
    <div class="row">
    <div class="form-group col-lg-11">
        <?= Html::submitButton($model->isNewRecord ? 'создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

