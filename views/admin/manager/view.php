<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model app\models\Manager */

$this->title = '"'.$model->name.'"';
$this->params['breadcrumbs'][] = ['label' => $backName, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view700 panel panel-info">

    <div class="panel-heading"><h3><?= Html::encode($this->title) ?></h3></div>
    <div class="panel-body">
    <p>
        <?= \Yii::$app->user->can('change')?Html::a('Изменить', ['change', 'id' => $model->id], ['class' => 'btn btn-primary']):'' ?>
        <?= \Yii::$app->user->can('remove')?Html::a('Удалить', ['remove', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить стороннего менеджера "'.$model->name.' ?',
                'method' => 'post',
            ],
        ]):'' ?>
    </p>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => $model->detailAttr
]) ?>
    </div>

</div>
