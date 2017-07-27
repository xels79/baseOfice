<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RBAC2acton
 *
 * @author Александр
 */
namespace app\rbac\actions;
use yii\base\Action;
use yii\helpers\Console;
class RBAC2action extends Action{
    private $ln=0;
    public function getE(){return "\r\n";}
    public function t($cnt=1){$r='';for(;$cnt>0;$cnt--)$r.="\t";return $r;}
    public function sp($c){$r='';for(;$c>0;$c--) $r.=' ';return $r;}
    public function rep($v,$c){
        $r='';
        for(;$c>0;$c--) $r.=$v;
        return $r;
    }
    protected function nln($cnt=1){
        $scr=Console::getScreenSize();
        //print_r($scr);
        $this->ln+=$cnt;
        if ($scr&&$this->controller->pages){
            if ($this->ln==$scr[1]-1){
                $this->ln=0;
                $this->controller->prompt( 'Для продолжения нажмите "enter"' );
                Console::moveCursorUp();
                Console::clearLine();
            }
        }
    }

    protected function selRoles($selectМultiple=false,$exclude=0,$roles=-1){
        $cr=&$this->controller;
        $authManager = \Yii::$app->authManager;
        $roles=$roles==-1?$authManager->roles:$roles;
        $cr->stdout(" Выбор роли.\r\n", Console::FG_CYAN,Console::BOLD);
        $it=[];
        foreach($roles as $r)
            if ($r->name!==$exclude) $it[]=['label'=>$r->name];
        $rVal=$this->showMenu($it,['selectМultiple'=>$selectМultiple,
                                   'returnValue'=>true,
                                   'showExit'=>'Отмена',
                                   'lineBack'=>2,
                                   'prompt'=>$selectМultiple?'Выберите номера ролей через пробел':
                                                             'Выберите номер роли'
                             ]);
        if ($rVal)
            if ($selectМultiple)
                return $rVal;
            else
                return($rVal[0]);
        else
            return 0;
    }
    protected function selPermis($selectМultiple=false,$permis=-1){
        $cr=&$this->controller;
        $authManager = \Yii::$app->authManager;
        $permis=$permis===-1?$authManager->permissions:$permis;
        $cr->stdout(" Выбор разрешения.\r\n", Console::FG_CYAN,Console::BOLD);
        $it=[];
        foreach($permis as $p)
            $it[]=['label'=>$p->name];
        $rVal=$this->showMenu($it,['selectМultiple'=>$selectМultiple,
                                   'returnValue'=>true,
                                   'showExit'=>'Отмена',
                                   'lineBack'=>2,
                                   'prompt'=>$selectМultiple?'Выберите номера разрешений через пробел':
                                                             'Выберите номер разрешения'
                             ]);
        if ($rVal)
            if ($selectМultiple)
                return $rVal;
            else
                return($rVal[0]);
        else
            return 0;        
    }
    protected function showHeader($txt='Управлени ролями и разрешениями.'){
        $cr=&$this->controller;
        //if ($cr->pages) $this->nln();
        $cr->stdout("\r\n\t".$txt."\r\n\r\n",  Console::FG_CYAN,Console::BOLD);
    }
    protected function showMenu($menu,$opt=[]){
        if (!isset($opt['selectМultiple'])) $opt['selectМultiple']=false;
        if (!isset($opt['prompt'])) $opt['prompt']='Выберите действие';
        if (!isset($opt['showExit'])) $opt['showExit']='Выход';
        if (!isset($opt['returnValue'])) $opt['returnValue']=false;
        if (!isset($opt['lineBack'])) $opt['lineBack']=3;
        if (!isset($opt['hideDetails'])) $opt['hideDetails']=false;
        $cr=&$this->controller;
      //  if ($clr) Console::clearScreen();
        $it=[];
        for($i=0;$i<count($menu);$i++){
            $cr->stdout(($i+1).'. ',  Console::FG_CYAN,Console::BOLD);
            $cr->stdout($menu[$i]['label']."\r\n",Console::FG_CYAN);
            $it[]=''.($i+1);
        }
        if ($opt['hideDetails']){
            $cr->stdout('H. Подробности = ',  Console::FG_CYAN,Console::BOLD);
            $cr->stdout(!$cr->hideDetails?'покозать':'скрыть', Console::FG_CYAN);
            echo "\r\n";
        }
        if ($opt['showExit']){
            $cr->stdout('0. ',  Console::FG_CYAN,Console::BOLD);
            $cr->stdout($opt['showExit']."\r\n", Console::FG_CYAN);
        }
        $cr->stdout($this->rep('▪',50)."\r\n", Console::FG_CYAN);
       // $cr->stdout('Выберите действие [ ]', Console::FG_CYAN);
       if ($opt['showExit']) $it[]='0';
       if ($opt['hideDetails']) {$it[]='H';$it[]='h';}
       $rVal=$this->select($it,$opt['prompt'],$opt['selectМultiple']);
       //print_r($rVal);
      if ($rVal) $this->curMUAC(count($it)+$opt['lineBack']);
       if ($opt['returnValue']){
           if ($opt['selectМultiple']){
               $tmp=[];
               foreach($rVal as $r){
                   if ($r!='h'&&$r!='H'){
                   $i=(int)$r-1;
                   if (isset($menu[$i]))
                       $tmp[]=$menu[$i]['label'];
                   }else
                       if ($opt['hideDetails']) $cr->hideDetails=!$cr->hideDetails;
               }
               $rVal=$tmp;
           }else{
               if ($rVal[0]==='h'||$rVal[0]==='H'){
                   if ($opt['hideDetails']) $cr->hideDetails=!$cr->hideDetails;
               }else
                    if (isset($menu[(int)$rVal[0]-1])) $rVal=[$menu[(int)$rVal[0]-1]['label']];
           }
       }elseif(($rVal[0]==='h'||$rVal[0]==='H')&&$opt['hideDetails'])
           $cr->hideDetails=!$cr->hideDetails;
       //print_r($rVal);\Yii::$app->end();
       return $rVal;
    }
    protected function select($it,$prompt,$selectMultiple){
        $cr=&$this->controller;
        echo "\r\n"; 
        do{
            $this->curMUAC();
            $cr->stdout($prompt.' [', Console::FG_CYAN);
            $o='';
            foreach($it as $i){
                if ($i!=='h'&&$i!=='H'){
                    if ($o!=='')$o.=',';
                    $o.=$i;
                }
            }
            $cr->stdout($o.'] ', Console::FG_CYAN);
            if ($selectMultiple) $cr->stdout(' (*-все) ', Console::FG_CYAN);
            $rVal=Console::input();
            if (!$selectMultiple)
                $chk=!in_array($rVal,$it);
            else {
                if ($rVal!=='*'){
                    $chk=true;
                    $rVal=explode(' ',$rVal);
                    for($i=0;$i<count($rVal)&&$chk;$i++)
                        $chk=!in_array($rVal[$i],$it);
                }else{
                    $chk=false;
                    $rVal=[];
                    for ($i=0;$i<count($it)-1;$i++)
                        $rVal[]=$it[$i];
                }
            }
        }while ($chk);
        if ($rVal) {
            $this->curMUAC();
            if (!$selectMultiple) $rVal=[$rVal];
        }
        return $rVal;
    }

    /*
     *Курсор вверх на $n строк с очисткой
     */
    public function curMUAC($n=1){
        for(;$n>0;$n--){
            Console::moveCursorUp();
            Console::clearLine();
        }
    }
    protected function CheckClassData($data){
	$contr=&$this->controller;
        $r=array_keys($data);
        $p=[];
        foreach($r as $rn){
            if (isset($data[$rn]['permissions']))
                foreach($data[$rn]['permissions'] as $pn)
                    if (!in_array($data[$rn], $p))
                        $p[]=$pn;            
        }
        foreach($r as $rn){
            $r[$rn]['c']=[];
            $r[$rn]['p']=[];
            $contr->stdout($this->e.'Роль "'.$rn.'":'. $this-> e,Console::FG_CYAN);
            if (isset($data[$rn]['permissions'])){
                $contr-> stdout("\tРазрешения: [",  Console::FG_PURPLE);
                foreach($data[$rn]['permissions'] as $pn){
                    if (!in_array($pn,$r[$rn]['p'])){
                        $contr->stdout($pn.', ',  Console::FG_GREEN);
                        $r[$rn]['p'][]=$pn;
                    }else{
                        $contr->eErr=' разрешение "'.$pn.'" уже задано!';
                        return false;
                    }                
                }
                $contr->stdout (']'.$this->e, Console::FG_PURPLE);
            }
                           
            if (isset($data[$rn]['childrens'])){
                $contr->stdout($this->e."\tПотомки:[",  Console::FG_BLUE);
                foreach ($data[$rn]['childrens'] as $cn){
                    if (!in_array($cn,$r[$rn]['c'])){
                        $contr->stdout($cn.', ',  Console::FG_GREEN);
                        $r[$rn]['c'][]=$cn;
                    }else{
                        $contr->eErr=' потомок "'.$cn.'" уже задан';
                        return false;
                    }
                }
                $contr->stdout(']'.$this->e,Console::FG_BLUE);
            }
        }
        return ['pNames'=>$p,'rNames'=>array_keys($data)];
    }
    protected function ClearAll($noAsk=false){
        if($rVal=$noAsk||$r_Val=Console::confirm($this->controller->ansiFormat($this->e.'Внимание!!!',Console::FG_RED).
                ' все настройки будут заменены')){
            $authManager = \Yii::$app->authManager;
            $authManager->removeAll();
        }
        return $rVal;
    }
    protected function argConvert($arg){
        if (is_array($arg))
            return $arg;
        else
            return explode(' ',$arg);
    }
    protected function getChildrenRoleTypeByRoleName($rName){
        $children=\Yii::$app->authManager->getChildren($rName);
        $rVal=[];
        foreach($children as $c)
            if ($c->type===1)
                $rVal[]=$c;
        return $rVal;
    }
}
