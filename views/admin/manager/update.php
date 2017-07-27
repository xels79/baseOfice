<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Manager */

    $this->beginContent('@app/views/layouts/adminAddChng.php',['vName'=>$vName,'backName'=>$backName,'model'=>$model,'addClass'=>'view700']);
?>
    <?= $this->render('_form', [
        'model' => $model,'backUrl'=>&$back['url']
    ]) ?>

<?php $this->endContent();?>
