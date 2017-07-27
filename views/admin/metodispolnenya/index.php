<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->beginContent('@app/views/layouts/adminListDet.php',['vName'=>$vName,'addClass'=>'view500']);
?>
    <p>
        <?= Html::a('Добавить', ['add'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'Показаны с {begin} по {end} Всего {totalCount}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name:ntext',

            ['class' => 'app\widgets\MActionColumn',
                'modelKeyToConfirm'=>'name',
                'confirm'=>'Удалить фирму "{info}" ?'
            ],
        ],
    ]); ?>
<?php
$this->endContent();
?>