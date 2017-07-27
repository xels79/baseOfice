<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Firms */

$this->title = $this->context->title.'/'.$vName;
$this->params['breadcrumbs'][] = ['label' => $backName, 'url' => $this->context->defaultBackUrlOption('list')];
$this->params['breadcrumbs'][] = $vName;
?>
<div class="firms-create">

    <h1><?= Html::encode($vName) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
