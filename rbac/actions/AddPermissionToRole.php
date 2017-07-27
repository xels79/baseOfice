<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddPermissionToRole
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
class AddPermissionToRole extends RBAC2action{
    public function run($roleN=0,$permis=0){
	$contr=&$this->controller;
        //echo 'Добовляем разрешения'.$this->e;
        if (!$roleN){
            $contr->stdout(" Добовляем разрешения.\r\n", Console::FG_CYAN,Console::BOLD);
            if (!$roleN=$this->selRoles())
                return;
        }
        if (!$permis){
            if (!$permis=$this->selPermis(true))
                return;
        } 
        $permis=$this->argConvert($permis);
        echo 'Добовляем разрешения к роли "'.$roleN.'":'.$this->e;
        $authManager = \Yii::$app->authManager;
        $role=$authManager->getRole($roleN);
        if($role){
            $i=0;
            foreach($permis as $permissionN){
                $contr->stdout($permissionN."\t",Console::FG_CYAN);
                if ($tmp2=$authManager->getPermission($permissionN)){
                    if (!array_key_exists($permissionN,$authManager->getPermissionsByRole($roleN))){
                        $authManager->addChild($role,$tmp2);
                        $contr->eEcho();
                        $i++;
                   }else{
                    $contr->eWarn=' разрешение уже задано игнорируется.';
                    $contr->eEcho();                    
                    }
                }else{
                    $contr->eWarn=' разрешение не нйдено игнорируется.';
                    $contr->eEcho();
                }
            }
            $contr->eOk='Добавлено '.$i.' разрешение(ия(ий)).';
        }else{
            //\Yii::$app->end();
            $contr->eErr='Роль не найдена.';
        }

    }
}
