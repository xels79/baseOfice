<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RmovePermissionFromSystem
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;

class RemovePermissionFromSystem extends RBAC2action{
    public function run($permissionNames=0){
      $contr=&$this->controller;
      $authManager = \Yii::$app->authManager;
       if (!$permissionNames){
           $contr->stdout("     Удаление  разрешения(ий) из системы.\r\n", Console::FG_CYAN,Console::BOLD);
           $permissionNames=$this->selPermis(true);
           if(!$permissionNames){
               $contr->eOk='Отмена.';
               return;
           }
       }
        $permissionNames=$this->argConvert($permissionNames);
       $i=0;
       foreach($permissionNames as $name){
          echo 'Удаляем разрешение ';
          $contr->stdout('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if(isset($authManager->permissions[$name])){
              //print_r($p);
             $authManager->remove($authManager->permissions[$name]);
             $i++;
             $contr->eEcho();
          }else{
             $contr->eWarn="не найдено, игнор.";
             if(count($tmp)>1) $contr->eEcho();
          }
       }
       $contr->eOk='Удалено '.$i.' разрешени(е(я(ий)))';
    }
}
