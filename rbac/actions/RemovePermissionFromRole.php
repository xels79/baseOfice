<?php
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
use yii\rbac\PhpManager;


class RemovePermissionFromRole extends RBAC2action{
    public function run($roleNames=0, $permissNames=0){
        $contr=$this->controller;
        if (!$roleNames){
            $contr->stdout(" Отменить разрешения у роли(ей).\r\n", Console::FG_CYAN,Console::BOLD);
            if (!$roleNames=$this->selRoles(true))
                return;
        }
        $roleN=$this->argConvert($roleNames);
        $permissNames=$permissNames?$permissNames=$this->argConvert($permissNames):0;
        $authManager = \Yii::$app->authManager;
        $tmpP=$permissNames?true:false;
        foreach($roleN as $rN){
            echo 'Обробатываем роль "'.$rN.'":'.$this->e;
            $role=$authManager->getRole($rN);
            if ($role){
                if (!$tmpP){
                    $permiss=$authManager->getPermissionsByRole($rN);
                    if (count($permiss)){
                        $permissNames=$this->selPermis(true,$permiss);
                        if (!$permissNames){
                            $permissNames=[];
                            $contr->eWarn =' отменено пользователём!';
                            $contr->eEcho();
                        }
                    }else{
                        $permissNames=[];
                        $contr->eWarn =' нет разрешений пропускаем!';
                        $contr->eEcho();
                    }
                }
                foreach($permissNames as $pN){
                    $contr->stdout($pN."\t-",Console::FG_CYAN);
                    if (array_key_exists($pN,$authManager->getPermissionsByRole($rN))){
                        $authManager->removeChild($role,$authManager->getPermissionsByRole($rN)[$pN]);
                        $contr->eOk=' разрешение отменено';
                    }else 
                        $contr->eWarn ='разрешение не задано игнорируется.';
                    $contr->eEcho();
                }
            }else{
                $contr->eErr='Роль не найдена.';
            }
        }
    }
}