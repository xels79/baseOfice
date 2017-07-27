<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
$urlOpr=['list'];
$urlOpt['page']=2;
$this->title = $this->context->title.'/'.$vName;
$this->params['breadcrumbs'][] = ['label' => $backName, 'url' => $this->context->defaultBackUrlOption('list')];
$this->params['breadcrumbs'][] = $vName;

?>
<h1><?= '"'.$model->name.'" - '.Html::encode($vName) ?></h1>