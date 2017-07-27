<?php
use yii\widgets\DetailView;
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title='Подробности заказа №'.$model->id;
$this->params['breadcrumbs'][] = ['label' =>$isBugalter?'Заказы бухгалтерия':'Заказы', 'url' => $this->context->zakazBackUrlOption($listBack)];
$this->params['breadcrumbs'][] = $this->title;
if (!$isBugalter)
    $this->params['breadcrumbs'][] = ['label' =>'Техничка', 'url' => $this->context->defaultUrlOption('details',['id'=>$model->id,'isDesiner'=>true,'fromDetali'=>true])];

?>
<div class="row hidden-print zakaz-detali-des2-but">
    <div class="col-xs-10">
        <div class="btn-group btn-group-xs">
            <?= $this->context->checkAccess($model)?Html::a('Загрузить файлы', $this->context->defaultUrlOption('change',['id'=>$model->id,'openField'=>'fileinput-mian']), ['class' => 'btn btn-success btn-xs hidden-print']):'' ?>
            <?= $this->context->checkAccess($model)?(($this->context->role==='admin')||($this->context->role==='moder'))?Html::a('Изменить', $this->context->defaultUrlOption('change',['id'=>$model->id]), ['class' => 'btn btn-primary']):'':'' ?>
        </div>
    </div>
</div>

<div class="row zakaz-detali">
    <div class="col-lg-8">
    <?=DetailView::widget([
        'model'=>$model,
        'attributes' => [
            'id',
            'oManager',
            'dateOfAdmission:date',
            [
                'label'=>'Заказчик',
                'value'=>$model->currentCustomerFirmName,
                'format'=>'text'
            ],
            'orderTypeTxt',
            'name',
            [
                'label'=>'Тираж',
                'value'=>$model->numberOfCopies.' шт.',
                'contentOptions'=>['title'=>'Всего: '.eval("return $model->numberOfCopies;").'шт.'],
                'format'=>'text'
            ],
            'formatProduct:text',
            [
                'format'=>'html',
                'label'=>$model->detaliSpecificationName,
                'value'=>$model->detaliSpecification
            ],
            [
                'label'=>'Исполнители',
                'value'=>$model->detaliExexCost(),
                'format'=>'html'
            ],
            'totalCost:currency',
            [
                'label'=>'Выплаты',
                'value'=>$model->detaliPayments,
                'format'=>'currency'
            ],
            [
                'label'=>'Прибыль',
                'value'=>$model->DetaliPribil,
                'format'=>'currency'
            ],
            'deadline:date',
        ],
    ]);?>
    </div>
    <?php if (!$isBugalter):?>
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
    <?php endif;?>

</div>