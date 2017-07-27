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
use yii\grid\GridView;
use app\widgets\JSRegister;
use yii\helpers\Json;


if (!$tblName=\yii::$app->request->get('tblName',false))
    if (!$tblName=\yii::$app->request->post('tblName',false))
            $tblName=\yii::$app->request->post('selMatMenu_DD-hidden',false);//selMatMenu_DD-hidden
$items= app\models\Materials::getAllMaterialsForList();
$this->registerJsFile($this->assetManager->publish('@app/web/js/dialog.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'zakazDialog');
$this->registerJsFile($this->assetManager->publish('@app/web/js/zakazMaterial.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'materialJS');
$ajaxupdaterequest=Url::to(['ajaxupdaterequest']);
$materialDetails=$model->mDetails;
//       \yii\helpers\VarDumper::dump($materialDetails,10,true);
//       \yii::$app->end();
$val=isset($materialDetails['value'])?$materialDetails['value']:[];
JSRegister::begin([
    'key' => 'zakazMaterial',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    var zMat=new ZakazMaterialController("<?=$ajaxupdaterequest?>",Zakaz.totalSumm,<?=Json::encode($val)?>);
</script>
<?php JSRegister::end();?>
<div class="zakaz-tab">
<div class="row">
    <div class="col-lg-3">
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
                    'noScript'=>false,
                    //'preventDefault'=>false,
                    'afterClickFunction'=>'zMat.matSelClick'
                    ])?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
            <div class="form-group">
                <label>ЗАКУПОЧНАЯ СТОИМОСТЬ МАТЕРИАЛА: </label>
                <?=Html::textInput('Zakaz[materialDetails][necessarySumm]',$materialDetails['necessarySumm'],['class'=>'form-control','readonly'=>true,'id'=>'materialDetails_summ'])?>
            </div>        
    </div>
    <div class="col-lg-4">
            <div class="form-group">
                <label>КОММЕРЧЕСКАЯ СТОИМОСТЬ МАТЕРИАЛА: </label>
                <?=Html::textInput('Zakaz[materialDetails][totalSumm]',$materialDetails['totalSumm'],['class'=>'form-control','id'=>'materialDetails_summKomerc','role'=>'totalCoast'])?>
            </div>        
    </div>
</div>
<?=Html::textInput('Zakaz[materialDetails][0]',null,['style'=>'display:none;','id'=>'materialDetails'])?>
<div class="row">
    <div id="sMaterial" class="form-horizontal">
    </div>
</div>
</div>