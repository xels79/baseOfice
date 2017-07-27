<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

/**
 * Description of _ZakazFile
 *
 * @author Александр
 */
class _ZakazFile extends _Zakaz{
    public function rules(){
        return array_merge(parent::rules(),[
            //[['fileInputUpLoad'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 4] //Для загрузки файлов заказчика
        ]);
    }
    public function upload(){
        $file=$this->fileInputUpLoad;
        if (!$file->saveAs(self::getZakazFileAlias($this->id).'/' . $file->baseName . '.' . $file->extension,false)){
            return $file->error;
        }else
            return false;
    }
    public function designerUpload(){
        $file=$this->fileInputUpLoad;
        if (!$file->saveAs(self::getDesignerFileAlias($this->id).'/' . $file->baseName . '.' . $file->extension,false)){
            return $file->error;
        }else
            return false;
    }
    public function publishZakazFile($name,$desiner=false){
        $path=realpath(\Yii::getAlias('@file').'/'.self::createZakazFolderName($this->id));
        $path.='/'.($desiner?'designer':'zakaz').'/'.$name;
        if (file_exists($path)){
            setlocale(LC_ALL, 'ru_RU.UTF-8');
            $tmp=\yii::$app->assetManager->publish($path);
            $tmp2=  pathinfo($tmp[0]);
            return [
                    'url'=>$tmp[1],
                    'path'=>$tmp[0],
                    'fullName'=>$name,
                    'ext'=>$tmp2['extension'],
                    'name'=>$tmp2['basename']
                ];
        }else
            return false;
    }
    public function fileList($filterExt='',$desiner=false){
         $path=realpath(\Yii::getAlias('@file').'/'.self::createZakazFolderName($this->id));
         $path.='/'.($desiner?'designer':'zakaz');
         
    }
    public static function getZakazFileAlias($id){
        $dir=\Yii::getAlias('@file').'/'.self::createZakazFolderName($id);
        if (!file_exists($dir)) mkdir($dir);
        $dir.='/zakaz';
        if (!file_exists($dir)) mkdir($dir);
        return $dir;
    }
    public static function getDesignerFileAlias($id){
        $dir=\Yii::getAlias('@file').'/'.self::createZakazFolderName($id);
        if (!file_exists($dir)) mkdir($dir);
        $dir.='/designer';
        if (!file_exists($dir)) mkdir($dir);
        return realpath($dir);//$dir;
    }
    public static function createZakazFolderName($id){
        $tmp=(string)$id;
        $rVal='';
        while(strlen($rVal)+strlen($tmp)<6){
            $rVal.='0';
        }
        return 'Z_'.$rVal.$tmp;
    }
    public function filterFiles($dir,$fileList,&$rVal){
        //$rVal=['fileList'=>[]];
        //$dir.='/';
        $totalSize=0;
        $cnt=0;
        foreach ($fileList as $fName){
            if (!is_dir($dir.'/'.$fName)){
                $size=filesize($dir.'/'.$fName);
                $rVal['fileList'][]=['name'=>$fName,'size'=>$size];
                $totalSize+=$size;
                $cnt++;
            }
        }
        $rVal['totalSize']=$totalSize; 
        $rVal['count']=$cnt;
        //return $rVal;
    }
    public function zakazFilesInfo(){
        $rVal=[
            'zakaz'=>['totalSize'=>0,'count'=>0,'fileList'=>[]],
            'designer'=>['totalSize'=>0,'count'=>0,'fileList'=>[]]
        ];
        $dir=\Yii::getAlias('@file').'/'.self::createZakazFolderName($this->id);
        if (file_exists($dir)){
            if (file_exists($dir.'/zakaz')) $this->filterFiles($dir.'/zakaz',scandir($dir.'/zakaz'),$rVal['zakaz']);
            if (file_exists($dir.'/designer')) $this->filterFiles($dir.'/designer',scandir($dir.'/designer'),$rVal['designer']);
        }
        //$rVal['dir']=$dir;
        $rVal['totalCount']=$rVal['zakaz']['count']+$rVal['designer']['count'];
        $rVal['totalSize']=$rVal['zakaz']['totalSize']+$rVal['designer']['totalSize'];
        return $rVal;
    }
    public function copyDirectory($dirS,$dirD){
        if (!file_exists($dirD)) mkdir($dirD);
        $dir=  opendir($dirS);
        while (($file = readdir($dir)) !== false){  
            // Если имеем дело с файлом - копируем его 
            if($file!='.'&&$file!='..') 
                if (is_file($dirS."/".$file))
                    copy($dirS."/".$file,$dirD."/".$file);
                else
                    $this->copyDirectory ($dirS."/".$file, $dirD."/".$file);
        }
        closedir($dir);
    }
    public function copyFilesFromZakaz($id){
        $dirS=\Yii::getAlias('@file').'/'.self::createZakazFolderName($id);
        if (file_exists($dirS)){
            $dirD=\Yii::getAlias('@file').'/'.self::createZakazFolderName($this->id);
            $this->copyDirectory($dirS, $dirD);
            return true;
        }else
            return false;
    }
    public static function countFile($path){
        $rVal=['count'=>0,'size'=>0,'ext'=>[]];
        if (file_exists($path)){
            if (is_dir($path)){
                $tmp=scandir($path);
                foreach($tmp as $fName){
                    if (!is_dir($path.'/'.$fName)){
                        $tmp2=pathinfo($path.'/'.$fName,PATHINFO_EXTENSION);
                        //$rVal['ext'][]=$tmp2;
                        if (!in_array($tmp2, $rVal['ext'])) $rVal['ext'][]=$tmp2;
                        $rVal['size']+=filesize($path.'/'.$fName);
                        $rVal['count']++;
                    }
                }
            }
        }
        return $rVal;
    }
    public function getHasfile(){
        $path=realpath(\Yii::getAlias('@file').'/'. \app\models\Zakaz::createZakazFolderName($this->id));
        $zakz=self::countFile($path.'/zakaz');
        $designer=self::countFile($path.'/designer');
        $title='';
        $ext=['pdf','ai','gif','jpg','jpeg','bmp','img','tiff','pdf','ttf','esp'];
        //$textDecor=' text-muted';
        //return \yii\helpers\VarDumper::dumpAsString($zakz['ext'],10,true);
        if ($zakz['count']||$designer['count']){
            $chk=false;
            foreach(array_merge($zakz['ext'],$designer['ext'])as $val){
                $chk=in_array($val, $ext);
                if ($chk) break;
            }
            
            //if (array_search($ext, $zakz['ext'])!=false||array_search($ext, $designer['ext'])!=false) $textDecor=' text-success';
            $options=['class'=>'glyphicon glyphicon-paperclip'];
            if ($chk) $options['style']='font-weight: bold;'; //$textDecor=' bg-info';
            if ($zakz['count']){
                $title.='Файлы заказчика: '.$zakz['count'].' шт. ('.\yii::$app->formatter->asShortSize($zakz['size'], 2).')';
            }
            if ($designer['count']){
                if ($title) $title.=', ';
                $title.=($title?'ф':'Ф').'айлы дизайнера: '.$designer['count'].' шт. ('.\yii::$app->formatter->asShortSize($designer['size'], 2).')';
            }
            if ($title!=='') $options['title']=$title;
            return \yii\helpers\Html::tag('span',null,$options);
        }else
            return '';
    }
}
