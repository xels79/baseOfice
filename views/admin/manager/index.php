<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->beginContent('@app/views/layouts/adminListDet.php',['vName'=>$vName,'addClass'=>'view700']);
?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'<span>Показаны с {begin} по {end} Всего {totalCount}</span>',
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name:ntext',
            'middle_name:ntext',
            'surname:ntext',
            ['class' => 'app\widgets\MActionColumn',
                'modelKeyToConfirm'=>'name',
                'confirm'=>'Удалить фирму "{info}" ?'
            ],
        ],
    ]); ?>
<?php
$this->endContent();
?>