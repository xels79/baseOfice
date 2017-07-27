<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreatePermission
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;

class CreatePermission extends RBAC2action{
    public function run($permissionNames=0){
      $contr=&$this->controller;
      $authManager = \Yii::$app->authManager;
       if (!$permissionNames){
           $contr->stdout("     Добовление  разрешения(ий) в систему.\r\n", Console::FG_CYAN,Console::BOLD);
           echo "Введите имя нового разрешения\n\rили несколько имён через пробел\r\n";
           $permissionNames=$contr->prompt('Пустая строка - отмена ? ');
           if(!$permissionNames){
               $contr->eOk='Отмена.';
               return;
           }
       }

      if (is_array($permissionNames))
          $tmp=$permissionNames;
       else
          $tmp=explode(' ',$permissionNames);
       $i=0;
       foreach($tmp as $name){
          echo 'Добовляем новое разрешение ';
          $contr->stdout('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if($authManager->getPermission($name)===null){
             $permis=$authManager->createPermission($name);
             $authManager->add($permis);
             $i++;
             $contr->eEcho();
          }else{
             $contr->eWarn="уже существует, игнор.";
             if(count($tmp)>1) $contr->eEcho();
          }
       }
       $contr->eOk='Добавлено '.$i.' разрешени(я(е(ий)))';
    }
}
