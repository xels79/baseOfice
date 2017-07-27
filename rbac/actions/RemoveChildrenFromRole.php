<?php
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
use yii\rbac\PhpManager;


class RemoveChildrenFromRole extends RBAC2action{
    public function run($roleNames=0, $childrenNames=0){
        $contr=$this->controller;
        if (!$roleNames){
            $contr->stdout(" Удалить роли потомки у роли(ей).\r\n", Console::FG_CYAN,Console::BOLD);
            if (!$roleNames=$this->selRoles(true))
                return;
        }
        $roleNames=$this->argConvert($roleNames);
        $childrenNames=$childrenNames?$childrenNames=$this->argConvert($childrenNames):0;
        $authManager = \Yii::$app->authManager;
        $tmpС=$childrenNames?true:false;
        foreach($roleNames as $rN){
            echo 'Обробатываем роль "'.$rN.'":'.$this->e;
            $role=$authManager->getRole($rN);
            if ($role){
                if (!$tmpС){
                    $children=$this->getChildrenRoleTypeByRoleName($rN);
                    if (count($children)){
                        //print_r($children);return;
                        $childrenNames=$this->selRoles(true,0,$children);
                        if (!$childrenNames){
                            $childrenNames=[];
                            $contr->eWarn =' отменено пользователём!';
                            $contr->eEcho();
                        }
                    }else{
                        $childrenNames=[];
                        $contr->eWarn =' нет потомков пропускаем!';
                        $contr->eEcho();
                    }
                }
                foreach($childrenNames as $cN){
                    $contr->stdout($cN."\t-",Console::FG_CYAN);
                    if (array_key_exists($cN,$authManager->getChildren($rN))){
                        $authManager->removeChild($role,$authManager->getChildren($rN)[$cN]);
                        $contr->eOk=' наследование отменено';
                    }else 
                        $contr->eWarn ='потомок не задан игнорируется.';
                    $contr->eEcho();
                }
            }else{
                $contr->eErr='Роль не найдена.';
            }
        }
    }
}