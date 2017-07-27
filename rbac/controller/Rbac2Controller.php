<?php

namespace app\rbac\controller;

use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use \app\rbac\UserGroupRule;
//use \app\rbac\UserProfileOwnerRule;
use yii\rbac\PhpManager;
use yii\helpers\BaseFileHelper;

const e="\r\n";
class Rbac2Controller extends Controller{
    public $eWarn=0;
    public $eErr=0;
    public $eOk=0;
    /*
     * Options:
     */
    public $hideDetails=false;
    public $pages=false;
    public $pathToFile='rbac';
    private $isConfigured=false;
    
    public function actions() {
        //parent::actions();
        return [
            'AddChildRole'=>'app\rbac\actions\ActionAddChildRole',
            'AddPermissionToRole'=>'app\rbac\actions\AddPermissionToRole',
            'Admin'=>'app\rbac\actions\Admin',
            'CreateFromClass'=>'app\rbac\actions\CreateFromClass',
            'CreatePermission'=>'app\rbac\actions\CreatePermission',
            'CreateRole'=>'app\rbac\actions\CreateRole',
            'ShowTree'=>'app\rbac\actions\ShowTree',
            'RemovePermissionFromRole'=>'app\rbac\actions\RemovePermissionFromRole',
            'RemoveChildrenFromRole'=>'app\rbac\actions\RemoveChildrenFromRole',
            'RemovePermissionFromSystem'=>'app\rbac\actions\RemovePermissionFromSystem',
            'RemoveRolesFromSystem'=>'app\rbac\actions\RemoveRolesFromSystem',
        ];
    }
   public function eEcho(){
      if ($this->eErr)
         $out=$this->ansiFormat(e.'Ошибка: '.$this->eErr.e,Console::FG_RED);
      elseif($this->eWarn)
         $out=$this->ansiFormat('Предупр.: '.$this->eWarn.e,Console::FG_YELLOW);
      else
         $out=$this->ansiFormat( ($this->eOk?$this->eOk:'Ok.'). e,Console::FG_GREEN );
    $this->eOk=0;
    $this->eWarn=0;
    $this->eErr=0;
    echo $out;
    if ($this->eErr)\Yii::$app->end();
   }
   
   public function  options($actionID){
       $rVal=parent::options($actionID);
       $rVal[]='hideDetails';
       $rVal[]='pages';
       $rVal[]='pathToFile';       
       return $rVal;
   }
  // public function setPath($val){
  //     $this->path=$val;
  // }
   public function init(){
      //\Yii::$app->authManager=new PhpManager(['itemFile'=>\Yii::getAlias($this->path.'/item.php'),]);
      $this->defaultAction='Admin';
      $this->on('afterAction', function($event) {
         $this->eEcho();
        });
       parent::init();
   }
   
   public function beforeAction($action){
    $rVal=parent::beforeAction($action);
    //BaseFileHelper
    if (!$this->isConfigured){
        \Yii::$app->authManager->itemFile=\Yii::getAlias('@app/'.$this->pathToFile).'/items.php';
        \Yii::$app->authManager->ruleFile=\Yii::getAlias('@app/'.$this->pathToFile).'/rules.php';
        $this->stdout( e.'Use path: '.$this->pathToFile.e.e, Console::FG_GREEN, Console::BOLD);
        if ($this->pathToFile==='rbac')
            $this->stdout("(Используй --pathToFile=\"путь к каталогу внутри каталога приложения/...\")\r\n",  Console::BOLD);
        else{
            $this->stdout(\Yii::$app->authManager->itemFile.e,Console::FG_GREEN);
            $this->stdout(\Yii::$app->authManager->ruleFile.e,Console::FG_GREEN);
        }
        \Yii::$app->authManager->init();
        $this->isConfigured=true;
    }
    return $rVal;
   }

    /*
     * Хэлп
     */
    public function getHelpSummary(){
        return 'Модуль для управления доступом через роли.';
    }
    public function getActionHelp($action){
        $rVal='';
        switch ($action->id){
            case 'create-frome-class':
                $rVal='Создаёт структуру ролей из указанного класса'.e.
                      'Класс должен находится в папке \\app\\rbac'.e;
                  
                 
                break;
            
        }
        return $rVal;
    }
    public function getActionHelpSummary($action){
        $rVal='';
        switch ($action->id){
            case 'CreateFromClass':
                $rVal=$this->t(2).'Создаёт структуру ролей из указанного класса.';
                 break;
            case 'AddChildRole':
                $rVal=$this->t(3).'Добовляет к роли потомка';
                 break;
            case 'AddPermissionToRole':
                $rVal=$this->t(2).'Добовляет к роли разрешение.';
                break;
            case 'Admin':
                $rVal=$this->t(2).'Управление ролями и разрешениями.';
                break;

            case 'CreatePermission':
                $rVal=$this->t(2).'Добовляет в набор разрешение';
                break;
            case 'CreateRole':
                $rVal=$this->t(3).'Добовляет в набор роли';
                break;
            case 'ShowTree':
                $rVal=$this->t(3).'Вывести дерево.';
                break;

            case 'RemovePermissionFromRole':
                $rVal=$this->t.'Удаляет разрешение у роли';
                break;
            case 'RemoveChildrenFromRole':
                $rVal=$this->t.'Удаляет потомков роли';
                break;
            case 'RemovePermissionFromSystem':
                $rVal=$this->t.'Удаляет разрешение из системы';
                break;
            case 'RemoveRolesFromSystem':
                $rVal=$this->t(2).'Удаляет роль(и) из системы';
                break;
        }
        return $rVal;
    }
    public function getActionArgsHelp($action){
        $rVal=parent::getActionArgsHelp($action);
        switch ($action->id){
            case 'ShowTree':
                $rVal['roleName']['comment']='Имя роли. Если не задано то вывод всех.';
                break;
            case 'CreateFromClass':
                $rVal['className']['comment']='Имя класса.';
                $rVal['className']['required']=true;
                break;
            case 'RemovePermissionFromRole':
                $rVal['roleNames']['comment']='* Имя роли или список в формате '.e
                    .'* "роль1 роль2 роль3 ..."';
                $rVal['roleNames']['type']='(string)';
                $rVal['permissNames']['comment']='* Имя разрешения или список в формате'.e
                    .'* "Разрешение1 Разрешение2 ..."';
                $rVal['permissNames']['type']='(string)';
                        
                break;
            
        }
        return $rVal;
    }
    public function getActionOptionsHelp($action){
        $rVal=parent::getActionOptionsHelp($action);
        
        $rVal['hideDetails']['comment']='Скрыть подробности';
        $rVal['hideDetails']['type']='boolean';
        $rVal['hideDetails']['default']=0;
       
        $rVal['pathToFile']['comment']='Путь к файлам items.php и rules.php';
        $rVal['pathToFile']['type']='string';
        $rVal['pathToFile']['default']='app\rbac';
        return $rVal;
        
    }
    public function getT(){return "\t";}
    public function t($cnt=1){$r='';for (;$cnt>0;$cnt--) $r.="\t";return $r;}
}
