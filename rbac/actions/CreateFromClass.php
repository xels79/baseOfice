<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateFromeClass
 *
 * @author Александр
 */
namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
class CreateFromClass extends RBAC2action{
    public function run($className){
        $class='\\app\\rbac\\'.$className;
        $contr=&$this->controller;
        $data=new $class;
        $roles=$data->run();
        $contr->stdout('Новая структура:'.$this->e,  Console::FG_CYAN);
        if ($p=$this->CheckClassData($roles)){
            if ($this->ClearAll()){
                $contr->runAction('CreatePermission',[ $p['pNames'] ]);
                $contr->eEcho();
                $contr->runAction('CreateRole',[ $p['rNames'] ]);
                $contr->eEcho();
                foreach($p['rNames'] as $rn){
                    if (isset($roles[$rn]['permissions'])){
                        $contr->runAction('AddPermissionToRole',[$rn,$roles[$rn]['permissions']]);
                        $contr->eEcho();
                    }
                    if (isset($roles[$rn]['childrens'])){
                        $contr->runAction('AddChildRole',[ $rn, $roles[$rn]['childrens'] ]);
                        $contr->eEcho();
                        $contr->eOk='Готово.';
                    }
                }
            }
        }

    }
}
