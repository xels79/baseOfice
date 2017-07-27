<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Manager */
/* @var $form yii\widgets\ActiveForm */

/*  Сторонний манеджер   */

$row1='col-xs-12 col-sm-4 col-md-4';
$row2='col-xs-7 col-sm-7 col-md-7';
$row3='col-xs-5 col-sm-5 col-md-5';
?>

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group row">
    <?= $form->field($model, 'name',['options'=>['class'=>$row1]])->textInput() ?>
    <?= $form->field($model, 'middle_name',['options'=>['class'=>$row1]])->textInput() ?>
    <?= $form->field($model, 'surname',['options'=>['class'=>$row1]])->textInput() ?>
    </div>
    <div class="form-group row">
    <?= $form->field($model, 'email',['options'=>['class'=>$row2]])->textInput(['maxlength' => 128]) ?>
    
    <?= $form->field($model, 'firmName',['options'=>['class'=>$row3]])->textInput(['readonly'=>true]) ?>
    </div>
    
    <div class="form-group row">
    <?= $form->field($model, 'fone')->hiddenInput()->label(false) ?>
    <?= app\widgets\ActiveFones::widget([
        'id'=>'manFones',
        'model'=>$model,
        'attribute'=>'fone',
        'buttonAddOptions'=>['class'=>'btn btn-success','style'=>'margin:3px 5px;'],
        'header'=>'Контактные телефоны'
        ]) ?>
    </div>
    <div class="form-group">
        <?= !\Yii::$app->request->isAjax?Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']):'' ?>
    </div>
    <?= $form->field($model, 'firm_id')->hiddenInput()->label(false)?>
    <?php ActiveForm::end(); ?>

