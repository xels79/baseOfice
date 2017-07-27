<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\widgets\ActiveFones;

/* @var $this yii\web\View */
/* @var $model app\models\Firms */
$this->beginContent('@app/views/layouts/adminListDet.php',['model'=>$model,'backName'=>$backName,'vName'=>$vName,'addClass'=>'view700']);
        $attributes= [
            'name:ntext',
            'typename'
        ];
        if ($model->productsTypes){
            $tmp=$model->materalsNames;
            if (isset($tmp,$model->productsTypes))
                    $attributes[]=['label'=>$model->attributeLabels()['productsTypes'],'value'=>$tmp[$model->productsTypes]];
        }
        if ($model->addres1) $attributes[]='addres1';
        if ($model->addres2) $attributes[]='addres2';
        $attributes[]=$model->foneCount?
            [
                'label'=>'Телефоны',
                'format'=>'raw',
                'value'=>app\widgets\ActiveFones::widget([
                    'model'=>&$model,
                    'attribute'=>'fone',
                    'buttonAddOptions'=>['class'=>'btn btn-success','style'=>'margin:3px 5px;'],
                    'editable'=>false,
                    'panel'=>false,
                    'short'=>true
                    ])
            ]:['label'=>'Телефоны','value'=>'Не указаны'];
?>
    <p> 
        <?= \Yii::$app->user->can('change')?Html::a('Изменить', ['change', 'id' => $model->id], ['class' => 'btn btn-primary']):'' ?>
        <?= \Yii::$app->user->can('remove')?Html::a('Удалить', ['remove', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить фирму "'.$model->name.' ?',
                'method' => 'post',
            ],
        ]):'' ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes
    ]) ?>
<?php $this->endContent(); ?>