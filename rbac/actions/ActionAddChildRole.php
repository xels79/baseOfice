<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ActionAddChildRole
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;

class ActionAddChildRole extends RBAC2action {
    public function run($roleN=0, $childNames=0){
	$contr=&$this->controller;
        if (!$roleN){
            $contr->stdout(" Добовbnm потомка(ов) к роли.\r\n", Console::FG_CYAN,Console::BOLD);
            if (!$roleN=$this->selRoles())
                return;
        }
        if (!$childNames){
            $contr->stdout(" Выберите потомков.\r\n", Console::FG_CYAN,Console::BOLD);
            if (!$childNames=$this->selRoles(true,$roleN))
                return;
        } 
        $childNames=$this->argConvert($childNames);
        echo 'Добовляем роль(и) потомка(ов) к роли "'.$roleN.'":'.$this->e;
        $authManager = \Yii::$app->authManager;
        $role=$authManager->getRole($roleN);
        if($role){
            $i=0;
            foreach($childNames as $childN){
                $contr->stdout($childN."\t",Console::FG_CYAN);
                if ($tmp2=$authManager->getRole($childN)){
                    if (!$authManager->hasChild($role,$tmp2)){
                        $authManager->addChild($role,$tmp2);
                        $contr->eEcho();
                        $i++;
                    }else{
                    $contr->eWarn=' потомок уже задан игнорируется.';
                    $contr->eEcho();                    
                    }
                }else{
                    $contr->eWarn=' роль потомок не нйдена игнорируется.';
                    $contr->eEcho();
                }
            }
            $contr->eOk='Добавлено '.$i.' потомок(а(ов)).';
        }else{
            //\Yii::$app->end();
            $contr->eErr='Роль родитель не найдена.';
        }

    }
}
