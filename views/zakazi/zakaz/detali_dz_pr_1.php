<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->title='Техничка заказа №'.$model->id;
$fromDetali=\yii::$app->request->get('fromDetali');
if (($this->context->role==='admin'||$this->context->role==='moder')&&$fromDetali){
    $this->params['breadcrumbs'][] = ['label' =>'Заказы', 'url' => $this->context->zakazBackUrlOption('list')];
}
if (!$fromDetali)
    $this->params['breadcrumbs'][] = ['label' =>!$isProizv?'Дизайнер':'Производство', 'url' => $this->context->zakazBackUrlOption($listBack)];
if (($this->context->role==='admin'||$this->context->role==='moder')&&$fromDetali){
    $this->params['breadcrumbs'][] = ['label' =>'Подробности заказа', 'url' => $this->context->defaultUrlOption('details',['id'=>$model->id])];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row hidden-print zakaz-detali-des2-but">
    <div class="col-xs-10">
        <div class="btn-group btn-group-xs">
            <?= $this->context->checkAccess($model)?(($this->context->role==='desiner')?Html::a('Загрузить файлы', $this->context->defaultUrlOption('deschange',['id'=>$model->id,'openField'=>'fileinput-mian']), ['class' => 'btn btn-primary']):((($this->context->role==='admin')||($this->context->role==='moder'))?Html::a('Изменить', $this->context->defaultUrlOption('change',['id'=>$model->id]), ['class' => 'btn btn-primary']):'')):'' ?>
            <?= Html::a('Печать', null, ['class' => 'btn btn-'.(($this->context->role==='proizvodstvo')?'primary':'default'),'id'=>'detaliPrint','onClick'=>'window.print();'])?>
        </div>
    </div>
</div>

<?php 
    if (($tmp=$model->detaliMaterialCount)<2)
        $items=[
            [
                'label'=>'Материал',
                'content'=>$this->render('technicals',['model'=>&$model,'elId'=>1])
            ]
        ];
    else{
        $items=[];
        for ($i=1;$i<=$tmp;$i++){
            $items[]=[
                'label'=>'Бумага №'.$i,
                'content'=>$this->render('technicals',['model'=>&$model,'elId'=>$i])
            ];
        }
        Yii::trace(\yii\helpers\VarDumper::dumpAsString($items),'detali_dz_pr_1');
    }
    echo Tabs::widget([
        'items'=>$items,
        'options' => ['class' => 'hidden-print'],
        'itemOptions' => ['tag' => 'div','class'=>'row zakaz-detali-des2'],
        //'headerOptions' => ['class' => 'hidden-print'],
//            'clientOptions' => ['collapsible' => false],
    ]);
?>
