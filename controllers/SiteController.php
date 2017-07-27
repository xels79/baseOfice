<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Zakaz;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;
use yii\helpers\Html;

class SiteController extends ControllerMain
{
    private $exportFile;

    public function beforeAction($action)
{
    if (parent::beforeAction($action)) {
        
        if (!$this->logOrErr){
            if ($this->role=='logist'){
                $this->redirect(['user/view','id'=>Yii::$app->user->identity->id]);
                return false;
            }
            elseif (($this->role!='admin'&&$this->role!='moder')&&!$this->logOrErr) {
                throw new ForbiddenHttpException('Недостаточно прав2');
            }
        }
//        $this->view->params['breadcrumbs'][]=[
//            'label'=>'На главную',
//            'url'=>Yii::$app->homeUrl
//        ];
        return true;
    } else {
        return false;
    }
}
    public function actionIndex()
    {
        $this->layout='main_2.php';
        $dependency = [
            'class' => 'yii\caching\DbDependency',
            'sql' => 'SELECT MAX(zakaz) FROM post',
        ];
        //if (!($countList=yii::$app->cache->get('$countList'))){
            $countList=[
                'all'=>Zakaz::find()->count(),
                'soglasovanye'=>Zakaz::find()->where(['stage'=>0])->count(),
                'disayn'=>Zakaz::find()->where(['stage'=>1])->count(),
                'print'=>Zakaz::find()->where(['stage'=>2])->count(),
                'handed'=>Zakaz::find()->where(['stage'=>3])->count(),
            ];
        //}
        return $this->render('index',['countList'=>$countList]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (\Yii::$app->user->identity->role==='logist'){
                return $this->redirect(['/zakazi/zakaz/logistlist']);
            }elseif (\Yii::$app->user->identity->role==='bugalter'){
                return $this->redirect(['/zakazi/zakaz/bugalterlist']);
            }elseif(\Yii::$app->user->identity->role==='desiner'||\Yii::$app->user->identity->role==='proizvodstvo'){
                return $this->redirect(['/zakazi/zakaz/deslist']);
            }else{
                if ($rUrl=Url::previous())
                    return $this->redirect(Url::previous());//$this->goBack();
                else
                    return $this->goHome();
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
//        if (!\Yii::$app->user->can('contact')) {
//            //throw new ForbiddenHttpException('Недостаточно прав');
 //           echo 'bidden<br>';
//        }

        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }
    public function actionBlocked()
    {
        if (!\yii::$app->params['isBlocked'])
            return $this->redirect ($this->brandUrl());
        $this->layout='mainBlocked.php';
        return $this->render('blocked');
    }
    public function actionTestsys(){
        $this->layout='main_2.php';
        return $this->render('testsys');
    }
    private function export($txt,$newLine=true){
        fwrite($this->exportFile, $txt.($newLine?"\r\n":""));
    }
    private function prepareTableToExport($tblName){
            $rVal='';
            $tCreate=\yii::$app->db->createCommand("show create table $tblName")->queryAll();
            $rVal.=$tCreate[0]['Create Table'].';';
            $dt=\yii::$app->db->createCommand("SELECT * FROM $tblName")->queryAll();
            $insertStr='INSERT INTO '.Yii::$app->db->quoteTableName($tblName).' (';
            $first=true;
            foreach ($dt as $el){
                $values=$first?'(':",(";
                $firstVal=true;
                foreach ($el as $k=>$val){
                    if ($first){
                        if ($insertStr[strlen($insertStr)-1]!=='(') $insertStr.=',';
                        $insertStr.=Yii::$app->db->quoteColumnName($k);
                    }
                    if ($firstVal){
                        $firstVal=false;
                        $values.=($val!==null?Yii::$app->db->quoteValue($val):'NULL');
                    }else{
                        $values.=($val!==null?','.Yii::$app->db->quoteValue($val):',NULL');
                    }
                }
                if ($first){
                    $first=false;
                    $insertStr.=') VALUES ';
                }
                $values.=")";
                $rVal.=$insertStr.$values;
                $insertStr='';
            }
            return $rVal.';';
    }
    public function actionExport(){
        $fn=  realpath(\yii::getAlias('@app/../../'.\yii::$app->params['sqlBackFolder']));
        if ($fn){
            $fn.='/'.\yii::$app->params['sqlBackFileName'];
            if ($this->exportFile=fopen($fn, 'w')){
                $this->export('set names utf8;');
                foreach (\yii::$app->db->schema->tableNames as $tblName)
                    $this->export($this->prepareTableToExport($tblName));
                fclose($this->exportFile);
                return Yii::$app->response->sendFile($fn, 'baseAsterion.sql');
                //return $this->refresh();
            }else{
                throw new \yii\web\NotFoundHttpException('Не возможно создать файл "'.$fn.'"');
            }
        }else{
            throw new \yii\web\BadRequestHttpException('Путь к "'.\yii::getAlias('@app/../../'.\yii::$app->params['sqlBackFolder']).'" ненайден');
        }
    }

}
