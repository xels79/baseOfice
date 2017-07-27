<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->registerJsFile($this->assetManager->publish('@app/web/js/zakazList.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listZakaz');
$this->registerJsFile($this->assetManager->publish('@app/web/js/materialList.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'materialList');
$this->registerJsFile($this->assetManager->publish('@app/web/js/dialog.js')[1],['depends' => [\yii\web\JqueryAsset::className()]],'listDialog');
$this->registerJs('var Zakaz= new ZakazLisController('
        . '{'
        . 'ajaxupdaterequest:"'.Url::to(['ajaxupdaterequest']).'",'
        . 'rqVarName:"rq",'
        . 'rqName:"list"'
        . '});',\yii\web\View::POS_READY,'zListStar');

    function n_sp(&$val,$del=', '){
        if (mb_strlen($val)) $val.=$del;
    }
    function td($content,$options=[]){return Html::tag('td',$content,$options);}
    function tdArr($arr){
        $rVal='';
        foreach($arr as $el){
            if (is_array($el)){
                $rVal.=td($el[0],$el[1]);
            }else{
                $rVal.=td($el);
            }
        }
        return $rVal;
    }
    function addIfSet(&$model,$key,&$rVal,$addText='',$brecked=false){
        //$rVal.=isset($model[$key])?n_sp($rVal).Html::tag('i',$model[$key].$addText):'';
        if (isset($model[$key])){
            n_sp($rVal);
            if ($brecked)
                $rVal.=Html::tag('i','('.$model[$key].$addText.')');
            else
                $rVal.=Html::tag('i',$model[$key].$addText);
        }
    }
    $this->beginContent('@app/views/layouts/zakazLOUT.php');
    $this->title='Материалы для производства';
    $this->params['breadcrumbs'][] = $this->title;
    $layout='{summary}<table class="table table-bordered"><tr>'
            . '<th>№</th><th>Заказчик</th>'
            . '<th colspan="2">Наименование</th>'
            . '<th>Кол-во</th><th>Поставщик</th>'
            . '<th>Стоимость</th>'
            . '<th>'.Html::tag('span',null,['class'=>'glyphicon glyphicon-info-sign','title'=>'Материал заказан']).'</th>'
            . '<th>'.Html::tag('span',null,['class'=>'glyphicon glyphicon-info-sign','title'=>'Материал получен']).'</th>'
            . '</tr>{items}</table>{pager}';
?>
<h2><?=$this->title?></h2>
    <?php Pjax::begin([
        'id'=>'listPjax',
        ])?>
    <?=ListView::widget([
        'dataProvider'=>$dataProvider,
        'summary'=>Html::tag('h4','<span class="text-primary">Всего: {totalCount} поз.</span>'),
        'options'=>[
            'class'=>'materialList'
        ],
        'itemOptions'=>[
            'tag'=>false
        ],
        'layout'=>$layout,
        'itemView'=>function ($model, $key, $index){
            $tmpData=$model->getDetaliMaterialNameSvodka2();
            $cnt=count($tmpData['value']);
            $rowspColOptions=[];
            if ($cnt>1) $rowspColOptions['rowspan']=$cnt;
            $rVal=tdArr([
                [$model->id,$rowspColOptions],
                [$model->getCurrentCustomerFirmName(),$rowspColOptions],
                ['<b>'.$tmpData['materTypeName'].'</b>',$rowspColOptions]
            ]);
            $tmpProvider=new \yii\data\ArrayDataProvider([
                'allModels'=>$tmpData['value']
            ]);
            $rVal.=ListView::widget([
                'dataProvider'=>$tmpProvider,
                'summary'=>false,
                'options'=>[
                    'tag'=>false
                ],
                'itemOptions'=>[
                    'tag'=>false
                ],
                'layout'=>'{items}',
                'emptyTextOptions'=>[
                    'tag'=>'td',
                    'colspan'=>5
                ],
                'emptyText'=>'Нет',
                'itemView'=>function($model, $key, $index,$widget){
                    \Yii::trace(\yii\helpers\VarDumper::dumpAsString($model),'materialList');
                    $rVal='';
                    addIfSet($model, 'paperName', $rVal);
                    //$rVal.=isset($model['paperName'])?n_sp($rVal).Html::tag('i',$model['paperName']):'';
                    addIfSet($model, 'colors', $rVal);
                    //$rVal.=isset($model['colors'])?n_sp($rVal).Html::tag('i',$model['colors']):'';
                    if (isset($model['pcolors'])&&$model['pcolors']!='нет')
                        addIfSet($model, 'pcolors', $rVal);
                    addIfSet($model, 'sizes', $rVal);
                    //$rVal.=isset($model['sizes'])?n_sp($rVal).Html::tag('i',$model['sizes']):'';
                    addIfSet($model, 'thickness', $rVal,'мкм');
                    //$rVal.=isset($model['thickness'])?n_sp($rVal).Html::tag('i',$model['thickness'].'мкм'):'';
                    addIfSet($model, 'density', $rVal,'г/м&#178;');
                    //$rVal.=isset($model['density'])?n_sp($rVal).Html::tag('i',$model['density'].'г/м&#178;'):'';
                    addIfSet($model, 'typeof', $rVal);
                    //$rVal.=isset($model['typeof'])?n_sp($rVal).Html::tag('i',$model['typeof']):'';
                    addIfSet($model, 'description', $rVal,'',true);
                    //$rVal.=isset($model['description'])?n_sp($rVal).Html::tag('i',$model['description']):'';
                    $rVal=td($rVal,['class'=>'listInfo']);
                    $rVal.=tdArr([
                        [isset($model['count'])?$model['count']:'',['class'=>"count"]],
                        isset($model['supplier'])?$model['supplier']:'',
                        isset($model['summ'])?$model['summ']:''
                    ]);
                    if (isset($model['dateOfOrder'])||isset($model['dateOfGet'])){
                        $rVal.=td(Html::tag('span',null,[
                            //'name'=>'ordered',
                            'material-index'=>$key,
                            'title'=>isset($model['dateOfOrder'])?'Материал заказан: '.Yii::$app->formatter->asDate($model['dateOfOrder']):'Материал не был заказан, но отмечено что он был получен?!',
                            'class'=>'glyphicon glyphicon-ok text-success',
                        ]));
                    }else{
                        $rVal.=td(Html::tag('span',null,[
                            //'name'=>'ordered',
                            'material-index'=>$key,
                            'title'=>'Материал не заказан',
                            'class'=>'glyphicon glyphicon-minus text-danger'
                        ]));
                    }
                    if (isset($model['dateOfGet'])){
                        $rVal.=td(Html::tag('span',null,[
                            //'name'=>'ordered',
                            'material-index'=>$key,
                            'title'=>'Материал получен: '.Yii::$app->formatter->asDate($model['dateOfGet']),
                            'class'=>'glyphicon glyphicon-ok text-success',
                            'data-placement'=>'left'
                        ]));
                    }else{
                        $rVal.=td(Html::checkbox('received',false,['material-index'=>$key]));
                    }
                    if ($index>0){
                        return Html::tag('tr',$rVal);
                    }else{
                        //$cntR=$widget->dataProvider->count;
                        return $rVal;
                    }
                }
            ]);
            $rVal.=$cnt==1?'</tr>':'';
            return Html::tag('tr',$rVal,['manName'=>$model->oManager,'data-key'=>$model->id]);
        }
    ])?>
    <?php Pjax::end()?>
<?=\yii\jui\DatePicker::widget([
    'name'  => '_dateHide',
    'options'=>['style'=>['display'=>'none']],
    'language' => 'ru',
]);?>
    <?php $this->endContent();?>