<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MethodOfExecution */
$this->beginContent('@app/views/layouts/adminListDet.php',['model'=>$model,'backName'=>$backName,'vName'=>$vName,'addClass'=>'view500']);
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
        'attributes' => [
            'id',
            'name:ntext',
        ],
    ]) ?>
<?php $this->endContent(); ?>