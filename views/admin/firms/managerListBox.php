<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo app\widgets\ActiveListBox::widget([
        'items'=>$managers,
        'id'=>'managerList',
        'formToSaveId'=>'firmForm',
        //'pageName'=>'Назад',
        'changeFunction'=>'$.fn.mManClickAddManager',
        'modalTarget'=>'Manager',
        'updateThisAction'=>\yii\helpers\Url::to(['admin/firms/ajaxupdaterequest','id'=>$modelId]),
        'addKeyId'=>'ManagerAddManager'
        //'removeAction'=>\yii\helpers\Url::to(['admin/manager/remove','id'=>$modelId]),
    ]);