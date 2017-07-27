<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
$this->title = $vName . ($model->name?' "' . $model->name.'"':'');
$this->params['breadcrumbs'][] = ['label' => $backName, 'url' => $this->context->defaultBackUrlOption('list')];
if (!$model->isNewRecord) $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => $this->context->defaultUrlOption('details', ['id' => $model->id])];
if (!$model->isNewRecord)
    $this->params['breadcrumbs'][] = 'Изменить';
else
    $this->params['breadcrumbs'][] = 'Добавить';
if (!isset($addClass)) $addClass='';
?>
<div class="panel panel-info <?=$addClass ?>">

    <div class="panel-heading"><h3><?= Html::encode($this->title) ?></h3></div>
    <div class="panel-body">
    <?=$content ?>
    </div>
</div>