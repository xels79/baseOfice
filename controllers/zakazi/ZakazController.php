<?php
namespace app\controllers\zakazi;

use Yii;

use app\controllers\ControllerMain;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\Zakaz;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
/**
 * FirmsController implements the CRUD actions for Firms model.
 */
class ZakazController extends ControllerMain
{
    public $defaultAction='list';
    private $listBack='list';
    private $ajaxRQ=[
        'managerList'=>'AjaxUpdateRequest',
        'addFirm'=>'AjaxAddFirm',
        'getFirmList'=>'AjaxFirmList',
        'addManager'=>'AjaxAddManager',
        'material'=>'AjaxMaterial',
        'list'=>'AjaxLisZakazUpdate',
        'buglist'=>'AjaxLisZakazBugalterUd',
        'mainLike'=>'MainLikeAction'
    ];
    public function beforeAction($action){
        $rVal=parent::beforeAction($action);
        if ($rVal){
            if ($this->role==='bugalter'&&$action->id==='logistlist'){
                throw new ForbiddenHttpException('Недостаточно прав2');
            }elseif(($this->role==='desiner'||$this->role==='proizvodstvo')&&($action->id==='logistlist'||$action->id==='bugalterlist')){
                throw new ForbiddenHttpException('Недостаточно прав2');
            }
        }
        if (!in_array($action->id, [
            'ajaxupdaterequest','upload','download','uploaddesigner','remfile','designerremfile'
        ])){
            Url::remember();
        }
        return $rVal;
    }
    public function actions() {
        $defAjaxAction='AjaxDefault';
        $errText='';
        if (\Yii::$app->request->isAjax){
            if ($rq=Yii::$app->request->post('rq')){
                if (array_key_exists($rq, $this->ajaxRQ)){
                    $defAjaxAction=$this->ajaxRQ[$rq];
                }else{
                    $errText='$rq="'.$rq.'" - нет такого запроса.';
                }
            }else{
                $errText='Незадан тип запроса "$rq"';
            }
        }else{
            $errText='Только AJAX!';
        }
        return array_merge(parent::actions(),[
            'ajaxupdaterequest'=>[
                'class'=>'app\controllers\zakazi\actions\\'.$defAjaxAction,
                'errorText'=>$errText
            ],
            'upload'=>[
                'class'=>'app\controllers\zakazi\actions\AjaxUpload'
            ],
            'download'=>[
                'class'=>'app\controllers\zakazi\actions\AjaxDownload'
            ],
            'uploaddesigner'=>[
                'class'=>'app\controllers\zakazi\actions\AjaxDesignerUpload'
            ],
            'remfile'=>[
                'class'=>'app\controllers\zakazi\actions\RemFile'
            ],
            'designerremfile'=>[
                'class'=>'app\controllers\zakazi\actions\DesignerRemFile'
            ]
        ]);
    }

    public function behaviors()
    {
        $rVal=parent::behaviors();
        $act=&$rVal['verbs']['actions'];
        $act['remfile']=['post'];
        $act['ajaxupdaterequest']=['post'];
        $act['find']=['post'];
        return $rVal;
    }

    public function actionList(){
        $this->layout='main_2.php';
        $searchModel = new \app\models\ZakazSearch();
        $dataProvider=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $tmpProv=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $this->checkAndSetLastPage($dataProvider,$tmpProv->query);
        return $this->render('list',['dataProvider'=>$dataProvider,'searchModel'=>$searchModel]);//@app/views/zakazi/zakaz/list.php
    }
    public function checkAccess(&$model){
        if ($this->role!=='admin'){
            if ($model->managerId===Yii::$app->user->identity->id){
                return true;
            }elseif(($this->role==='proizvodstvo'||$this->role==='desiner')&&$this->action->id==='ajaxupdaterequest'){
                if (Yii::$app->request->post('materialDateParam')) return true;
                if ($tmp=Yii::$app->request->post('attrToSave')){
                    if (($tmp[0]==='stage'&&count($tmp)===1))
                        return true;
                    else
                        return false;
                }else return false;
            }else{
                return false;
            }
        }  else {
            return true;
        }
    }
    private function renderForm(&$model){
        $isDesiner=\yii::$app->request->get('isDesiner');
        return $this->render('_form',[
            'model'=>$model,
            'header'=>'Изменить заказ №'.$model->id,
            'listBack'=>$this->listBack,
            'isDesiner'=>$isDesiner
        ]);        
    }
    public function actionAjaxvalidate(){
        $model=new Zakaz;
        if (\Yii::$app->request->isAjax && $model->load(\Yii::$app->request->post())){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }else
            return '';
    }
    public function actionChange($id){
        if ($this->role==='admin'||$this->role==='moder') $this->layout='main_2.php';
        if ($model=Zakaz::findOne($id)){
            if ($isDesiner=\yii::$app->request->get('isDesiner'))
                $this->listBack='deslist';
            if ($isProizv=\yii::$app->request->get('isProizv'))
                $this->listBack='deslistadmin';
            if ($this->role!=='desiner'){
                if (!$this->checkAccess($model, $id)){
                    throw new ForbiddenHttpException('Нельзя редактировать чужой заказ');
                }
            }
            
            if ($model->load(Yii::$app->request->post())){
                if ($this->checkAccess($model, $id)){
                    if ($model->save()){
                        return $this->redirect($this->zakazBackUrlOption($this->listBack));
                    }else{
                        return $this->renderForm($model);
                    }
                }else{
                    throw new ForbiddenHttpException('Нельзя редактировать чужой заказ');
                }
            }
            return $this->renderForm($model);
        }else{
            throw new NotFoundHttpException('Заказ №'.$id.' не найден!');
        }
    }
    public function actionAdd(){
        $this->layout='main_2.php';
//        $this->layout='main_2_1.php';
        $model = new Zakaz();
        $model->managerId=\yii::$app->user->id;
        $alert=false;
        if ($model->load(Yii::$app->request->post(),'Zakaz')) {
            if ($model->save()){
                if (\yii::$app->request->post('copyFromOld')){
                    if ($parentZakazId=\yii::$app->request->post('parentZakazId',0)){
                        if ($model->copyFilesFromZakaz($parentZakazId))
                            $alert='Скопированны файлы из заказа №'.$parentZakazId;
                        else
                            $alert='Ошибка при копирование файлов из заказа №'.$parentZakazId;
                    }
                }
                return $this->redirect($this->defaultBackUrlOption('details',['id'=>$model->id]));
            }
            return $this->render('_form',['model'=>$model,'listBack'=>$this->listBack,'isDesiner'=>false]);
        } else {
            $model->accountNumber='Не выставлено';
            return $this->render('_form',['model'=>$model,'listBack'=>$this->listBack,'isDesiner'=>false]);        
        }
    }
    public function actionRemove($id){
        if ($model=Zakaz::findOne($id)){
            if (!$this->checkAccess($model, $id)){
                throw new ForbiddenHttpException('Нельзя удалить чужой заказ');
            }
            $model->delete();
        }
        return $this->redirect($this->zakazUrlOption('list'));
    }
    public function actionDetails($id){
        if ($this->role==='bugalter')
            $isBugalter=true;
        else
            $isBugalter=$this->getOrPost('isBugalter',false);
        if ($this->role==='admin'||$this->role==='moder'){
            $this->layout='main_2.php';
            $actionPoiz='deslistadmin';
        }else{
            $actionPoiz='deslist';
        }
        if ($isDesiner=\yii::$app->request->get('isDesiner')){
            $this->listBack='deslist';
            $isProizv=false;
        }elseif($isProizv=\yii::$app->request->get('isProizv'))
            $this->listBack=$actionPoiz;
        if ($this->role==='proizvodstvo'||$this->role==='desiner'||$isDesiner||$isProizv){
            $fName='detali_dz_pr_1';
        }else{
            $fName='detali';
        }
        if ($isBugalter) $this->listBack='bugalterlist';        
        if ($model=Zakaz::findOne($id)){
            if (\yii::$app->request->isAjax||\yii::$app->request->isPost){
                return $this->renderPartial($fName,['model'=>$model, 'listBack'=>$this->listBack, 'isBugalter'=>$isBugalter, 'isProizv'=>$isProizv]);
            }else{
                return $this->render($fName,['model'=>$model, 'listBack'=>$this->listBack, 'isBugalter'=>$isBugalter, 'isProizv'=>$isProizv]);
            }
        }else
            throw new NotFoundHttpException('Заказ №'.$id.' не найден!');
    }
    public function actionCopy($id){
        if ($this->role==='admin'||$this->role==='moder') $this->layout='main_2.php';
        if ($model=Zakaz::findOne($id)){
//            Yii::$app->end();
            $tmp=$model->toArray();
            unset($tmp['id']);
            $tmp['managerId']=\yii::$app->user->identity->id;
            $newModel=new Zakaz($tmp);
            $newModel->materialDetails=$tmp['materialDetails'];
            $newModel->parameters=$tmp['parameters'];
            //$newModel->load($tmp);
            return $this->render('_form',['model'=>$newModel,'submitText'=>'Копировать','header'=>'Копируем заказ! Исходный заказ №'.$model->id,'actionId'=>'add', 'listBack'=>$this->listBack,'parentZakazId'=>$model->id,'copyFiles'=>$model->zakazFilesInfo(),'isDesiner'=>false]);
           //return $this->renderContent(\yii\helpers\VarDumper::dumpAsString($newModel,10,true));
        }else
           throw new NotFoundHttpException('Заказ №'.$id.' не найден!');
       
    }
    public function actionDeslistadmin(){
        return $this->actionDeslist(true);
    }

    public function actionDeslist($isProizv=false){
//   Yii::$app->mailer->compose()
//         ->setFrom('zakaz@asterionspb.ru')
//        ->setTo('xel_s@mail.ru')
//         ->setSubject('Тестовое сообщение')
//         ->send();
        //$isDesiner=\yii::$app->request->get('isDesiner',$this->role==='proizvodstvo'||$this->role==='desiner');
        if (!$isProizv)
            $isProizv=Yii::$app->user->identity->role==='proizvodstvo';
        if (Yii::$app->user->identity->role==='admin'||Yii::$app->user->identity->role==='moder') $this->layout='main_2.php';
        $searchModel = new \app\models\ZakazSearchDes(['isProizv'=>$isProizv]);
        $dataProvider=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $tmp=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $this->checkAndSetLastPage($dataProvider,$tmp->query);
        return $this->render('desygnerList',['dataProvider'=>$dataProvider,'searchModel'=>$searchModel,'isProizv'=>$isProizv]);//@app/views/zakazi/zakaz/list.php
        
    }
    public function actionLogistlist(){
        if ($this->role==='admin'||$this->role==='moder') $this->layout='main_2.php';
        return $this->renderContent('Таблица для Логистики ещё не потдерживается');
    }
    public function actionBugalterlist(){
        $cacgePerfix=Yii::$app->id.$this->role;
        if ($this->role==='admin') $this->layout='main_2.php';
        elseif($this->role!='bugalter')
            throw new ForbiddenHttpException('Недостаточно прав');
        $searchModel = new \app\models\ZakazSearchBugalter();
        $queryParam=!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post();
        $dataProvider=$searchModel->search($queryParam);
        $tmp=$searchModel->search($queryParam);
        $this->checkAndSetLastPage($dataProvider,$tmp->query);
        unset($tmp);
        $QAsString=\yii\helpers\VarDumper::dumpAsString(\yii\helpers\ArrayHelper::getValue($queryParam,'ZakazSearchBugalter'),10);
        if (Yii::$app->cache->get($cacgePerfix.'oldQAsString')!==$QAsString||!($pribParam=Yii::$app->cache->get($cacgePerfix.'bugalterPribParam'))){//
            
            Yii::trace('Сохраняем в кэш','ZakazControllerActionBugalterlist');
            Yii::$app->cache->set($cacgePerfix.'oldQAsString',$QAsString);
            Yii::trace($QAsString,'ZakazControllerActionBugalterlist');
            $query=$searchModel->search($queryParam)->query;
            $query->select([
                'totalCost',
                'contractors',
                'materialDetails'
            ]);
            $tmp=$query->all();
            unset($query);
            $pribParam=[
            'totalProfit'=>0,
            'summMat'=>0,
            'summExec'=>0,
            'summBonus'=>0,
            'totalSumm'=>0,
            ];
            foreach ($tmp as $el){
                $prib=$el->prib;
                $pribParam['totalProfit']+=$prib['summ'];
                $pribParam['summMat']+=$prib['summMat'];
                $pribParam['summExec']+=$prib['summExec'];
                $pribParam['summBonus']+=$prib['summBonus'];
                $pribParam['totalSumm']+=(double)$el->totalCost;
            }
            $dependency = new \yii\caching\DbDependency;
            $dependency->sql = 'SELECT MAX(lastChange) FROM zakaz';
            Yii::$app->cache->set($cacgePerfix.'bugalterPribParam',$pribParam,null,$dependency);
        }else{
            Yii::trace('Кеш без изменений','ZakazControllerActionBugalterlist');
        }

        return $this->render('bugalterlist',['dataProvider'=>$dataProvider,'searchModel'=>$searchModel,'totalProfit'=>$pribParam['totalProfit'],'summMat'=>$pribParam['summMat'],'summExec'=>$pribParam['summExec'],'summBonus'=>$pribParam['summBonus'],'totalSumm'=>$pribParam['totalSumm']]);
    }
    public function zakazBackUrlOption($actionId=false,$opt=[]){
        $rVal=$this->defaultBackUrlOption($actionId,$opt);
        if ($ZakazSearch=$this->getOrPost('ZakazSearch')){
            $rVal['ZakazSearch']=$ZakazSearch;
        }
        return $rVal;
    }
    public function zakazUrlOption($actionId=false,$opt=[]){
        $rVal=$this->defaultUrlOption($actionId,$opt);
        if ($ZakazSearch=$this->getOrPost('ZakazSearch')){
            $rVal['ZakazSearch']=$ZakazSearch;
        }
        return $rVal;
    }
    public function actionDeschange($id){
        $this->listBack='deslist';
        return $this->actionChange($id);
    }
    public function actionMateriallist(){
        $this->layout='main_2.php';
//        $searchModel = new \app\models\ZakazSearch();
//        $dataProvider=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $query = \app\models\ZakazMaterialOrder::find()
                ->where(['is_material_ordered'=>null,'all_material_recived'=>0])
                ->andWhere(['like','materialDetails','"value":']);
        $dataProvider=  new ActiveDataProvider(['query'=>$query]);
        return $this->render('materialList',['dataProvider'=>$dataProvider]);
    }
    public function actionFind(){
        $id=\yii::$app->request->post('id');
        $actionId=\yii::$app->request->post('actionId');
        $isBugalter=\yii::$app->request->post('isBugalter',false);
        if ($id){
            if ($model=Zakaz::findOne($id)){
                $this->redirect(['details','id'=>$id,'isBugalter'=>$isBugalter]);
            }
        }
        if (\yii::$app->request->isPost){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!$id){
                $errorText='Номер заказа не задан';
            }elseif (is_numeric($id)){
                $errorText='Заказ "'.$id.'" не найден';
            }else{
                $errorText='Номер заказа должен быть числом.';
            }
            return ['error'=>'Not found','errorText'=>$errorText];
        }else{
            throw new NotFoundHttpException('Заказ "'.$id.'" не найден');
            return false;
        }
    }
    public function actionProizvmaterial(){
        if (Yii::$app->user->identity->role==='admin'||Yii::$app->user->identity->role==='moder') $this->layout='main_2.php';
        $searchModel = new \app\models\ZakazSearchDes(['isProizv'=>true]);
        $dataProvider=$searchModel->search(!Yii::$app->request->isPost?Yii::$app->request->queryParams:Yii::$app->request->post());
        $dataProvider->query->andWhere(['like','materialDetails','"value":']);
        return $this->render('proizvmaterials',['dataProvider'=>$dataProvider]);
    }
}