<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    use yii\helpers\Html;
    if (isset($model)) 
        $this->title ='"'.$model->name.'"';
    else
        $this->title=$vName;
    if (isset($backName)) $this->params['breadcrumbs'][] = ['label' => $backName, 'url' => $this->context->defaultBackUrlOption('list')];
    $this->params['breadcrumbs'][] = $this->title;
    if (!isset($addClass)) $addClass='';
?>
<div class="panel panel-info <?=$addClass ?>">

    <div class="panel-heading"><h3><?= Html::encode(isset($backName)?$vName.': '.$this->title:$this->title) ?></h3></div>
    <div class="panel-body">
    <?=$content ?>
    </div>
</div>