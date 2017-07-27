<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\ActiveDropdown;
use app\widgets\Panel;

//$items=[
//    'paper'=>['label'=>'Бумага','url'=>['zakazi/material','tblName'=>'paper']]
//];
$items= app\models\Materials::getAllMaterialsForList();
$this->registerJsFile($this->assetManager->publish('@app/web/js/material.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'materialJS');
?>
<?php Panel::begin([
    'panelHeader'=>'Управление материалами',
    'options'=>['class'=>'mat-edit']
    ]);?>
    <div class="row">
        <?=isset($addUrl)?Html::a('Добавить',$addUrl,['class'=>'btn btn-success btn-sm']):''?>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-controll">
                <div class="form-group">
                    <label>Выберите материал: </label>
                    <?= ActiveDropdown::widget([
                        'id'=>'selMatMenu_DD',
                        'selected'=>$tblName?$tblName:false,
                        'label'=>$tblName?$items[$tblName]['label']:false,
                        'menuId'=>'selMatMenu',
                        'attribute'=>'tblName',
                        'items'=>$items,
                        'preventDefault'=>false,
                        'afterClickFunction'=>'materialSelClick'
                        ])?>
                </div>
            </div>
        </div>
        <div class="col-sm-6"><?=$content?></div>
    </div>
<?php Panel::end();?>