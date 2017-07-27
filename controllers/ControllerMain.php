<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use \yii\helpers\Html;

class ControllerMain extends Controller
{
    protected $logOrErr=false;
    protected $role='guest';
    public $mMenu=[];
    public $title;
    public $contSz=0;
    public $backId=0;
    
    public function init(){
        parent::init();
        $this->title=Yii::$app->name;
    }
    public function getRole(){return $this->role;}
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    private function createMessageList(){
        $rVal=[];
        $safety=[];
        $time=0;
        if ($tmp=$this->checkLastBaseBackUp($time)){
            if ($time>-1)
                $time=\yii::$app->formatter->asRelativeTime($time);
            else
                $time=false;
            if ($tmp<3){
                $safety[]=[
                    'label'=>'<span class="glyphicon glyphicon-bell"></span>Сохранить БД',
                    'encode'=>false,
                    'url'=>Url::to(['site/export']),
                    'linkOptions'=>[
                        'data-content'=>$time!==false?"База данных сохранялась $time!":"База ниразу не сохранялась!",
                        'id'=>'savebase',
                        'class'=>'nav-mess'
                    ],
                    
                ];
            }else{
                $safety[]=[
                    'label'=>'Нет временной папки для сохранения БД',
                    'url'=>'#',
                    'linkOptions'=>['disabled'=>true]
                ];                
            }
        }
        if ($cnt=count($safety)){
            $messTxt=' сообщ';
            if ($cnt===1){
                $messTxt.='ение';
            }elseif($cnt<5){
                $messTxt.='ния';
            }else{
                $messTxt.='ений';
            }
            $rVal[]=Html::tag('li',$cnt.$messTxt.' безопастности',['class'=>'dropdown-header']);
            foreach($safety as $el)
                $rVal[]=$el;
        }
        return $rVal;
    }
    private function showMessage(){
        $mess=$this->createMessageList();
        if ($messCnt=count($mess)){
            $this->mMenu[]=[
                'options'=>['class'=>'nav-mess-main'],
                'label'=>'<span class="glyphicon glyphicon-envelope"></span><span> Сообщения </span><span class="badge badgePrimary">'.($messCnt-1).'</span>',
                'encode'=>false,
                'items'=>$mess
            ];
        }        
    }
    private function setupMenu(){
        $user=Yii::$app->user->identity;
//        $messPress=false;
        $cachePostFix=Yii::$app->id.$user->username;
        if (!$messPress=$this->checkLastBaseBackUp()){
            if ($this->mMenu=\yii::$app->cache->get('mMenu'.$cachePostFix)){
                \yii::trace('Загружено из кэша','mMenu');
                return;
            }else{
                $this->mMenu=[];
            }
        }else{
            \yii::$app->cache->delete('mMenu'.$cachePostFix);
            \yii::trace('Удалено из кэша','mMenu');
        }
        if ($user->role==='admin'&&(\yii::$app->request->hostInfo==='http://93.185.189.210:28080'||\yii::$app->request->hostInfo==='http://192.168.1.42')){
            $this->mMenu[]=['label'=>'Тест','url'=>['/site/testsys']];
        }
        if ($user->role!=='desiner'&&$user->role!=='proizvodstvo'){
            if ($this->role!='logist'&&$this->role!='bugalter'){
//                $this->mMenu[]=['label'=>'Менеджер','url'=>['/zakazi/zakaz/list']];
//                $this->mMenu[]=['label'=>'Дизайнер','url'=>['/zakazi/zakaz/deslist']];
            }
//            if ($user->role!=='bugalter') $this->mMenu[]=['label'=>'Логистика','url'=>['/zakazi/zakaz/logistlist']];
//            if ($user->role!=='logist') $this->mMenu[]=['label'=>'Бухгалтерия','url'=>['/zakazi/zakaz/bugalterlist']];
            if ($this->role=='admin'||$this->role=='moder'){
                $this->mMenu[]=['label' => 'Сотрудники', 'url' => ['/user/index']];            
            }
        }else{
            $this->mMenu[]=['label'=>'Заказы','url'=>['/zakazi/zakaz/deslist']];
        }
        if ($user->role==='proizvodstvo'){
            $this->mMenu[]=['label'=>'Материалы','url'=>['/zakazi/zakaz/proizvmaterial']];
        }
        $userMenu=[
            ['label' => 'Профиль', 'url' => ['/user/view','id'=>Yii::$app->user->identity->id]],
            ['label' => 'Выйти из системы' ,
                'url' => ['/site/logout'],
                'linkOptions' => ['data-method' => 'post'],
            ],
        ];
        if ($this->role==='admin'){
            $this->showMessage ();
            $userMenu[]='<li class="divider"></li>';
            $userMenu[]=['label' => 'Сохранить базу', 'url' => ['/site/export']];
        }
        $this->mMenu[]=[
            'label'=>'('.(!$user->realname?$user->username:$user->realname).')',
            'items'=>$userMenu
            ];
        \yii::trace('Создано','mMenu');
        if (!$messPress){
            \yii::$app->cache->set ('mMenu'.$cachePostFix, $this->mMenu,1200);
            \yii::trace('Сохранено в кэш','mMenu');
        }  else {
            \yii::trace('Не сохранено имеются сообщения!','mMenu');
        }
    }
    protected function setHomeUrl(){
        if (!\Yii::$app->user->isGuest)
            switch (\Yii::$app->user->identity->role){
                case 'logist':
                    \yii::$app->homeUrl=  Url::to(['/zakazi/zakaz/logistlist']);
                    break;
                case 'bugalter':
                    \yii::$app->homeUrl=  Url::to(['/zakazi/zakaz/bugalterlist']);
                    break;
                case 'desiner':
                case 'proizvodstvo':
                    \yii::$app->homeUrl=  Url::to(['/zakazi/zakaz/deslist']);
                    break;
            }
    }
    public function beforeAction($action)
{
    if (!\Yii::$app->user->isGuest)
        if (\Yii::$app->user->identity->role!=='logist'&&\Yii::$app->user->identity->role!=='bugalter'){
            if ($action->id!=='index'||$action->controller->id!=='site'){
                $this->view->params['breadcrumbs'][]=[
                    'label'=>'На главную',
                    'url'=>$this->brandUrl()
                ];        
            }else{
                $this->view->params['breadcrumbs'][]='Главная';
            }
    }
    if (parent::beforeAction($action)) {
        $this->setHomeUrl();
        $this->logOrErr=$action->id=='logout'||$action->id=='login'||$action->id=='error'||$action->id=='blocked';
        if (!\Yii::$app->user->isGuest){
            //if ($userI);
            $this->role=\Yii::$app->user->identity->role;
            if ($action->id!=='blocked') 
                $this->setupMenu();
            else{
                $this->mMenu=[];
                $this->mMenu[]=[
                    'label'=>'('.(!Yii::$app->user->identity->realname?Yii::$app->user->identity->username:Yii::$app->user->identity->realname).')',
                    'items'=>[
                            ['label' => 'Выйти из системы' ,
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post'],
                            ],
                        ]
                    ];                
            }
        }elseif (!$this->logOrErr){
            $this->redirect(['site/login']);
            return false;
        }
        if (!\Yii::$app->user->can($action->id)&&$action->id!=='error'&&$action->id!=='blocked'&&!($this->action->id==='ajaxupdaterequest'&&$this->role==='proizvodstvo')) {
            throw new ForbiddenHttpException('Недостаточно прав11');
        }
        if (\yii::$app->params['isBlocked']&&\yii::$app->user->id!=1&&!\Yii::$app->user->isGuest){
            if ($action->id!=='blocked') $this->redirect(['site/blocked']);
        }
        $this->view->registerJs('$.fn.enablePopover();',\yii\web\View::POS_READY,'enblPopover');
        $this->view->registerJs('$.fn.enableButtons();',\yii\web\View::POS_READY,'enblButton');        
        return true;
    } else {
        return false;
    }
}
    public function afterAction($action, $result) {
        $rVal=parent::afterAction($action, $result);
        return $rVal;
    }
    public function getOrPost($name,$default=null){
        if (($rVal=yii::$app->request->get($name,$default))===null){
            $rVal=yii::$app->request->post($name,$default);
        }
        return $rVal;
    }
    public function defaultUrlOption($actionId=false,$opt=[]){
        $rVal=$opt;
        if ($actionId)
            $rVal[0]=$actionId;
        if ($backId=$this->getOrPost('backId')){
            $rVal['backId']=$backId;
        }
        return $rVal;
    }
    public function defaultBackUrlOption($actionId=false,$opt=[]){
        $rVal=$this->defaultUrlOption($actionId,$opt);
        if ($id=$this->getOrPost('id')){
            $rVal['backId']=$id;
            $rVal['#']=$id;
        }
        return $rVal;
    }
    protected function checkAndSetLastPage(&$dataProvider,$query,$defSrcStr='`zakaz`.`id`'){
        if ($bId=$this->getOrPost('backId')){
            (int)$number=0;
            (int)$this->backId=(int)$bId;
            $query->andWhere("$defSrcStr<=$this->backId");
            if (is_numeric($bId)){
                if ($this->role!=='desiner'&&$this->role!=='proizvodstvo'){
                    $number=(int)$query->count();
                }else{
                    if ($this->role!=='proizvodstvo')
                            $query->andFilterWhere(['stage'=>[0,1,2]]);
                        else
                            $query->andFilterWhere(['stage'=>2]);
                    $number=(int)$query->count();
                }
            }
            //$dataProvider->query=$queryBack;
            Yii::info('Порядковый номер вычесл.SQL: '.\yii\helpers\VarDumper::dumpAsString($number-1),'checkAndSetLastPage');
            $nPg=intval(($number-1)/($dataProvider->pagination->pageSize));
            Yii::info('Страница номер: '.\yii\helpers\VarDumper::dumpAsString($nPg),'checkAndSetLastPage');
            $dataProvider->pagination->page=$nPg;
            \yii\helpers\ArrayHelper::remove($_GET,'page');
            \yii\helpers\ArrayHelper::remove($_GET,'backId');
        }
    }
    public function brandUrl(){
        if (\Yii::$app->user->can('index')){
            return Yii::$app->homeUrl;
        }elseif (\Yii::$app->user->can('deslist')){
            return Url::to(['/zakazi/zakaz/deslist']);
        }else{
            return '#';
        }
    }
    private function checkLastBaseBackUp(&$time=null){
        $fn=realpath(\yii::getAlias('@app/../../'.\yii::$app->params['sqlBackFolder']));
        if ($time!==null)$time=-1;
        if ($fn){
            $fn.='/'.\yii::$app->params['sqlBackFileName'];
            if (file_exists($fn)){
                if ($time!==null){
                $time=filectime($fn);
                $t=time()-$time;
                }else{
                    $t=time()-filectime($fn);
                }
                if ($t>\yii::$app->params['saveBaseTimeOut'])
                    return 1;//пора обновить
                else
                    return 0;//всё ок
            }else return 2;//Файл не создан
        }return 3;//Путь не найден
    }
    public function sideMenu(){
        if (!$rVal=Yii::$app->cache->get('sideMenu'.$this->action->id.$this->role.Yii::$app->id,false)){
            $items=[
                    [
                        'label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-book']).'Заказы'  ,
                        'items'=>[
                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-list-alt']).'Менеджера','url'=>['/zakazi/zakaz/list']],
                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-camera']).'Дизайнера','url'=>['/zakazi/zakaz/deslist']],
                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-wrench']).'Производство','url'=>['/zakazi/zakaz/deslistadmin']],
                        ],
                        'options'=>['class'=>\app\widgets\MNav::getWasActive()?'open':'']
                    ],
                    [
                        'label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-tasks']).'Материалы'  ,
                        'items'=>[
                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-briefcase']).'Для заказа','url'=>['/zakazi/zakaz/materiallist']],
                            ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-wrench']).'В производ.','url'=>['/zakazi/zakaz/proizvmaterial']],
                        ],
                    ],
                    ['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-plane']).'Логистика','url'=>['/zakazi/zakaz/logistlist']],
                ];
            if ($this->role=='admin')
                $items[]=['label'=>Html::tag('span',null,['class'=>'glyphicon glyphicon-usd']).'Бухгалтерия','url'=>['/zakazi/zakaz/bugalterlist']];
            $rVal=\app\widgets\MNav::widget([
                'options' => ['class' => 'nav navbar-nav side-nav side-nav-open'],
                //'id'=>'left-nav-menu',
                'activateItems'=>true,
                'activateParents'=>true,
                'encodeLabels'=>false,
                'items' => $items,

            ]);
            Yii::trace("Боковое меню ('sideMenu".$this->action->id.$this->role."')\nРоль: '$this->role'\nЭкшен: '".$this->action->id."\nСоздано и сохранено в кэш",'sideMenu');
            Yii::$app->cache->set('sideMenu'.$this->action->id.$this->role.Yii::$app->id, $rVal,3600);
        }else{
            Yii::trace("Боковое меню ('sideMenu".$this->action->id.$this->role."')\nРоль: '$this->role'\nЭкшен: '".$this->action->id."\nЗагружено из кэша",'sideMenu');
        }
        return $rVal;
    }
}
