<?php

namespace app\rbac\actions;
use app\rbac\actions\RBAC2action;
use yii\helpers\Console;
use \app\rbac\UserGroupRule;

class Admin extends RBAC2action{
    private $mMenu=[
        ['label'=>'Просмотр дерева',
         'action'=>'ShowTree'
        ],
        ['label'=>'Добавить роль(и)',
         'action'=>'CreateRole'
        ],
        ['label'=>'Добавить разрешние(я)',
         'action'=>'CreatePermission'
        ],
        ['label'=>'Добавить разрешение к роли',
         'action'=>'AddPermissionToRole'
        ],
        ['label'=>'Добавить потомков к роли',
         'action'=>'AddChildRole'
        ],
        ['label'=>'Отменить разрешения у ролей',
         'action'=>'RemovePermissionFromRole'
        ],
        ['label'=>'Отменить потомков у ролей',
         'action'=>'RemoveChildrenFromRole'
        ],
        ['label'=>'Удалить разрешение(я) из системы',
         'action'=>'RemovePermissionFromSystem'
        ],
        ['label'=>'Удалить роль(и) из системы',
         'action'=>'RemoveRolesFromSystem'
        ]

    ];
    
    public function run(){
     
       $contr=&$this->controller;
       do{
           //Console::clearScreen();
           $this->showHeader();
           $m=$this->showMenu($this->mMenu,['hideDetails'=>true]);
           $contr->pages=true;
           if ($m){
            if (isset($this->mMenu[$m[0]-1])){
                $this->showHeader();
                $contr->runAction($this->mMenu[$m[0]-1]['action']);
                if ($m) $contr->prompt( 'Для продолжения нажмите "enter"' );   
            }
           }
       }while ($m); 
    }
}