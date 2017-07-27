<?php

namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
use yii\rbac\PhpManager;

class ShowTree extends RBAC2action{
    private $tb=0;
    private $chkOne=0;
    private $permisInUse=[];
    private function arrPermissChild($roleN){
        $authManager = \Yii::$app->authManager;
        $children=$authManager->getChildren($roleN);
        $rVal=[];
       // print_r($children);\Yii::$app->end();
        foreach($children as $c)
            if ($c->type==2)
                if (isset($rVal['permission']))
                    $rVal['permission'][]=$c;
                else
                    $rVal['permission']=[$c];
            elseif($c->type==1)
                if (isset($rVal['children']))
                    $rVal['children'][]=$c;
                else 
                    $rVal['children']=[$c];
        return $rVal;
    }
    public function run($roleName=''){
        $contr=$this->controller;
        if (!$this->chkOne++) $contr->stdout('Дерево ролей и разрешений:'.$this->e ,Console::FG_CYAN, Console::BOLD);
        $authManager = \Yii::$app->authManager;
        $roles=$authManager->roles;
        if ($roleName){
           echo $this->sp($this->tb);
           $this->nln();
           if (!$this->tb)
               $contr->stdout($this->e.'Роль "'.$roleName.'":',Console::FG_CYAN,Console::BOLD);
           else 
               $contr->stdout('Потомок "'.$roleName.'":',Console::BOLD);
           $this->tb++;
           echo $this->e;
           $this->nln();
           if (isset($roles[$roleName])){
               $c=$this->arrPermissChild($roleName);
               if (isset($c['permission'])){
                   $contr->stdout($this->sp($this->tb).'Разрешения : [',Console::FG_GREEN);
                   foreach($c['permission']as $p){
                       $contr->stdout($p->name.', ',Console::FG_GREEN);
                       if (!in_array($p->name, $this->permisInUse)) 
                               $this->permisInUse[]=$p->name;
                   }
                   $contr->stdout(']'.$this->e,Console::FG_GREEN);
                   $this->nln();
               }
               if (isset($c['children']))
                    if ($contr->hideDetails){
                        $contr->stdout($this->sp($this->tb).'Потомки : [',Console::FG_CYAN);
                        foreach($c['children']as $p)
                            $contr->stdout($p->name.', ',Console::FG_CYAN);
                        $contr->stdout(']'.$this->e,Console::FG_CYAN);
                        $this->nln();
                    }else{
                        foreach($c['children']as $p)
                            $this->run($p->name);
                    }
           }else{
               $contr->eErr='Роль ненайдена.';
           }
           $this->tb--;
           //echo $this->e;
       }else{
           foreach($roles as $r)
               $this->run($r->name);
           
       }
       $this->chkOne--;
       if (!$this->chkOne){
            $out='';
            foreach($authManager->permissions as $p)
                if (!in_array($p->name,$this->permisInUse))
                    $out.=$p->name.', ';    
                    //$contr->stdout($p->name.', ',Console::FG_GREEN);
            if ($out){
                $this->nln(2);
                $contr->stdout($this->e.'Не используемые',Console::FG_RED);
                $contr->stdout(' разрешения : [ '.$out.']'.$this->e,Console::FG_GREEN);
                //$contr->stdout(']'.$this->e,Console::FG_GREEN);
            }
       }
    }
}