<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateRole
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
use \app\rbac\UserGroupRule;

class CreateRole extends RBAC2action{
    public function run($roleNames=0){
       $contr=&$this->controller;
       $authManager = \Yii::$app->authManager;
       if (!$roleNames){
           $contr->stdout("\t Добовление роли(ей) в систему.\r\n", Console::FG_CYAN,Console::BOLD);
           echo "Введите имя добовляемой роли\n\rили несколько имён через пробел\r\n";
           $roleNames=$contr->prompt('Пустая строка - отмена ? ');
           if(!$roleNames){
               $contr->eOk='Отмена.';
               return;
           }
       }
       $roleNames=$this->argConvert($roleNames);
       $i=0;
       //print_r($tmp);
       foreach($roleNames as $name){
           echo 'Добовляем новую роль ';
           $contr->stdout('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if($authManager->getRole($name)===null){
             $role=$authManager->createRole($name);
             $userGroupRule = new UserGroupRule();
             $authManager->add($userGroupRule);
 
        // Add rule "UserGroupRule" in roles
             $role->ruleName  = $userGroupRule->name;
             $authManager->add($role);
             $i++;
             $contr->eEcho();
          }else{
             $contr->eWarn="уже существует, игнор.";
             if(count($tmp)>1) $contr->eEcho();
          }
       }
    }
}
