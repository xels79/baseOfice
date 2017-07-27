<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RemoveRolesFromSystem
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;

class RemoveRolesFromSystem extends RBAC2action{
    public function run($roleNames=0){
      $contr=&$this->controller;
      $authManager = \Yii::$app->authManager;
       if (!$roleNames){
           $contr->stdout("     Удаление  роли(ей) из системы.\r\n", Console::FG_CYAN,Console::BOLD);
           $roleNames=$this->selRoles(true);
           if(!$roleNames){
               $contr->eOk='Отмена.';
               return;
           }
       }
        $roleNames=$this->argConvert($roleNames);
       $i=0;
       foreach($roleNames as $name){
          echo 'Удаляем роль ';
          $contr->stdout('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if(isset($authManager->roles[$name])){
              //print_r($p);
             $authManager->remove($authManager->roles[$name]);
             $i++;
             $contr->eEcho();
          }else{
             $contr->eWarn="не найдено, игнор.";
             if(count($tmp)>1) $contr->eEcho();
          }
       }
       $contr->eOk='Удалено '.$i.' роль(и(ей))';
    }
}
