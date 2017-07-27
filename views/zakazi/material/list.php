<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\ActiveDropdown;
use app\widgets\Panel;
use yii\grid\GridView;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$this->beginContent('@app/views/layouts/zakazLOUT.php');
if ($tblName)
    $addUrl=['add','tblName'=>$tblName];
else
    $addUrl=['add'];

$this->beginContent('@app/views/layouts/materialLOU.php',['tblName'=>$tblName,'addUrl'=>$addUrl]);
?>
<div class="row">
<?=$model&&$dataProvider?GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'Показаны с {begin} по {end} Всего {totalCount}',
        'columns' => $model->gridViewColumns,
    ]):'' ?>
</div>
<?php $this->endContent(); ?>