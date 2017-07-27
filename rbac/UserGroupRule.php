<?php
namespace app\rbac;
 
use Yii;
use yii\rbac\Rule;
 
class UserGroupRule extends Rule
{
    public $name = 'userGroup';
    private $opt=['testOpt'=>'test'];
    public function setData($val){
        $this->data=$val;
    }
    public function addOptions(){
    }
    private function RuleArray(){
        $cache=\Yii::$app->cache;
        if(!$tmp=$cache->get('userGroup')){
            $authManager = \Yii::$app->authManager;
            $tmp=[];
            foreach($authManager->getRoles() as $rule)
                $tmp[]=$rule->name;
        }
        $cache->set('userGroup',$tmp,120);
        return $tmp;        
    }
    public function execute($user, $item, $params=[])
    {
    $rVal=false;
    $roles=$this->RuleArray();        
     if (!\Yii::$app->user->isGuest) {
         $role = \Yii::$app->user->identity->role;
         if ($role==='admin') return true;
         for($i=count($roles)-1;$i>=0&&!$rVal;$i--){
             for($n=$i;$n<count($roles)&&!$rVal&&$item->name===$roles[$i];$n++)
                $rVal=$role==$roles[$n];
     //echo 'НЕ ясно.';\Yii::$app->end();
         }
     }else
        $rVal=$item->name==$roles[0];
     if ($rVal){
         if (isset($params['roles'])){
             if (!\Yii::$app->user->isGuest)
                $rVal=in_array(\Yii::$app->user->identity->role,$params['roles']);
             else
                 $rVal=false;
         }elseif(isset($params['idCheck'])){
             if (!\Yii::$app->user->isGuest)
                $rVal=\Yii::$app->user->id==$params['idCheck'];
             else
                 $rVal=false;
            
         }
     }
     return $rVal;
    }
}