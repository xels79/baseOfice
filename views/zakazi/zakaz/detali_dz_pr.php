<?php
use yii\widgets\DetailView;
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->params['breadcrumbs'][] = ['label' =>'Заказы', 'url' => $this->context->zakazBackUrlOption($listBack)];
$this->params['breadcrumbs'][] = 'Заказ №'.$model->id;

?>
test2
<p>
    <?= ($this->context->role==='desiner')?Html::a('Загрузить файлы', $this->context->defaultUrlOption('deschange',['id'=>$model->id,'openField'=>'fileinput-mian']), ['class' => 'btn btn-success btn-xs hidden-print']):'' ?>
</p>

<div class="row zakaz-detali-des">
    <div class="col-lg-8">
        <table class="table table-bordered">
            <tr>
                <th width='80px'>Заказ №:</th>
                <td width='65px'><?=$model->id?></td>
                <th width='90px'>Менеджер:</th>
                <td width='200px'><?=$model->oManager?></td>
                <th width='90px'>Заказчик:</th>
                <td><?=$model->firmNameById($model->firmIdByManager)?></td>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr>
                <th width='80px'>Продукция:</th>
                <td width='220px'><?=$model->orderTypeTxt?></td>
                <th width='90px'>Кол-во:</th>
                <td width='90px'><?=$model->numberOfCopies?></td>
                <th width='90px'>Формат:</th>
                <td><?=$model->detaliFormat_Size?></td>
            </tr>
            <tr>
                <th>Бумага:</th>
                <td colspan="5"><?=$model->detaliPaperName?></td>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr>
                <th width='105px'>Формат блока:</th>
                <td width='160px'><?=$model->detaliBlockFormat?></td>
                <th width='110px'>Кол-во блоков:</th>
                <td width='60px'><?=$model->detaliBlockCount?></td>
                <th width='60px'>Уф-лак:</th>
                <td><?=$model->detaliUfLak?></td>
            </tr>
        </table>
        <table class="table table-bordered">
            <tr>
                <th width='105px' rowspan="2">Цветность:</th>
                <td width='90px'><?=$model->detaliGetFaceTypeText?></td>
                <td><?=$model->detaliFaceContent?></td>
            </tr>
            <tr>
                <td width='90px'><?=$model->detaliGetBackTypeText?></td>
                <td><?=$model->detaliBackContent?></td>
            </tr>
            <?=$model->detaliPospechat?>
        </table>
    </div>
    <div class="col-lg-4">
        <?=app\widgets\FileList::widget([
            'id'=>'fileManager1',
            'headerContent'=>'Файлы заказчика',
            'options'=>['class'=>'file-loaded hidden-print file-loaded-detail'],
            'zakazId'=>$model->id,
            'downloadAction'=>'download',
            'pageSize'=>12
        ])?>
        <?=app\widgets\FileList::widget([
              'id'=>'fileManager2',
              'headerContent'=>'Файлы дизайнера',
              'options'=>['class'=>'file-loaded hidden-print file-loaded-detail'],
              'zakazId'=>$model->id,
              'downloadAction'=>'download',
              'pageSize'=>12,
              'isInputFiles'=>false
        ])?>

    </div>    
</div>