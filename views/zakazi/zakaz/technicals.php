<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
?>
<div class="visible-print row">
    <p class="center-block">Материал №"<?=$elId?>"</p>
</div>
    <div class="col-lg-10">
        <table class="table table-bordered table1">
            <tr>
                <th colspan="2">Заказ №:</th>
                <td><?=$model->id?></td>
                <th>Заказчик:</th>
                <td><?=$model->firmNameById($model->firmIdByManager)?></td>
                <th>Менеджер:</th>
                <td><?=$model->oManager?></td>
                <th>Срок:</th>
                <td><?=\yii::$app->formatter->asDate($model->deadline)?></td>
            </tr>
            <tr class="noBorder"><td colspan="9"></td></tr>
            <tr>
                <th colspan="2">Продукция:</th>
                <td colspan="3" class="info"><?=$model->orderTypeTxt?></td>
                <th>Тираж:</th>
                <td><?=$model->numberOfCopies?></td>
                <th>Формат:</th>
                <td><?=$model->detaliProductFormat?></td>
            </tr>
            <tr>
                <th colspan="2">Наименование:</th>
                <td colspan="7" class="info"><?=$model->name?></td>
            </tr>
            <tr>
                <th colspan="2">Бумага:</th>
                <td colspan="3"><?=$model->getDetaliPaperName($elId)?></td>
                <th>Плотность:</th>
                <td><?=$model->getDetaliDensity($elId)?></td>
                <th>Кол-во:</th>
                <td><?=$model->getDetaliCount($elId)?></td>
            </tr>
            <tr>
                <th colspan="2">Формат блока:</th>
                <td colspan="3"><?=$model->detaliBlockFormat?></td>
                <th>Кол-во блока:</th>
                <td><?=$model->detaliBlockCount?></td>
                <th>Уф-лак:</th>
                <td><?=$model->detaliUfLak?></td>
            </tr>
            <tr>
                <th rowspan="2" class="colors">Цвет:</th>
                <td><?=$model->detaliGetFaceTypeText?></td>
                <td colspan="7"><?=$model->detaliFaceContent?></td>
            </tr>
            <tr>
                <td><?=$model->detaliGetBackTypeText?></td>
                <td colspan="7"><?=$model->detaliBackContent?></td>
            </tr>
            <?=$model->detaliPospechat2?>
            <tr>
                <th colspan="2">Примечание:</th>
                <td colspan="7" style="font-size:20px;color:red;"><?=$model->param['rem']?></td>
            </tr>
            <tr>
                <td colspan="9"><table>
                        <tr><td></td><td><?=$model->detaliFormatLista['width']?></td><td></td><td><?=$model->detaliFormatLista['width']?></td></tr>
                        <tr><td><?=$model->detaliFormatLista['height']?></td><td><div class="liff"></div></td><td><?=$model->detaliFormatLista['height']?></td><td><div class="liff"></div></td></tr>
                </table></td>
            </tr>
            <?=$model->getDetaliFWork($elId)?>

        </table>
    </div>
<div class="col-lg-2">
    <?=app\widgets\FileList::widget([
        'id'=>'fileManager1',
        'headerContent'=>'Файлы заказчика',
        'options'=>['class'=>'file-loaded hidden-print file-loaded-detail'],
        'zakazId'=>$model->id,
        'downloadAction'=>'download',
        'shortHeader'=>true,
        'maxButtonCount'=>1,
        'pageSize'=>12
    ])?>
    <?=app\widgets\FileList::widget([
          'id'=>'fileManager2',
          'headerContent'=>'Файлы дизайнера',
          'options'=>['class'=>'file-loaded hidden-print file-loaded-detail'],
          'zakazId'=>$model->id,
          'downloadAction'=>'download',
          'shortHeader'=>true,
          'pageSize'=>12,
          'isInputFiles'=>false
    ])?>

</div>    