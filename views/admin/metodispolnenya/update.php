<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MethodOfExecution */
    $this->beginContent('@app/views/layouts/adminAddChng.php',['vName'=>$vName,'backName'=>$backName,'model'=>$model,'addClass'=>'view500']);
    echo $this->render('_form', [
        'model' => $model,'backUrl'=>&$back['url']
    ]);
    $this->endContent();

