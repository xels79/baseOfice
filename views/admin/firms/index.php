<?php
use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Button;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FirmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->beginContent('@app/views/layouts/adminListDet.php',['vName'=>$vName,'addClass'=>'view700']);
?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= Html::a('Добавить',$this->context->defaultBackUrlOption('add'),[
        'class'=>'btn btn-success',
    ])?>

    <?= GridView::widget([
        'options'=>['class'=>'view700'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary'=>'<p>Показаны с {begin} по {end} Всего {totalCount}</p>',
        'columns' => [
            ['class' => 'app\widgets\MSerialColumn'],
            'name:ntext',
            [
                'attribute' => 'searchtype',
                'filter'=>[
                    0=>'Заказчик',
                    1=>'Исполнитель',
                    2=>'Поставщик'
                ],
                'value'=>'typename'
            ],
            ['class' => 'app\widgets\MActionColumn',
                'modelKeyToConfirm'=>'name',
                'confirm'=>'Удалить фирму "{info}" ?',
                'otherParam'=>$this->context->defaultUrlOption()
            ],
        ],
    ]); ?>
<?php $this->endContent(); ?>