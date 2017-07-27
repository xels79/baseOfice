<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Firms */

    $this->beginContent('@app/views/layouts/adminAddChng.php',['vName'=>$vName,'backName'=>$backName,'model'=>$model,'addClass'=>'firms-update']);
?>
    <?= $this->render('_form', [
        'model' => $model//,'pName'=>$this->title
    ]) ?>
<?php $this->endContent(); ?>