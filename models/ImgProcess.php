<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of _ImageProcess
 *
 * @author Александр
 */
namespace app\models;
use \Imagick;
class ImgProcess {
    private static function checkSz(&$img,$maxWidth,$maxHeight){
            $dl=0;
            if($img->getimagewidth()>$maxWidth){
                $dl=$img->getimagewidth()/$maxWidth;
            }elseif ($img->getimageheight()>$maxHeight){
                $dl=$img->getimageheight()/$maxHeight;
            }
            if ($dl){
                $img->resizeImage(round($img->getimagewidth()/$dl), round($img->getimageheight()/$dl), \Imagick::FILTER_LANCZOS, 1);
//                if($img->getimagewidth()>$maxWidth){
//                    $dl=$img->getimagewidth()/$maxWidth;
//                }elseif ($img->getimageheight()>$maxHeight){
//                    $dl=$img->getimageheight()/$maxHeight;
//                }
//                if ($dl){
//                    $img->resizeImage(round($img->getimagewidth()/$dl), round($img->getimageheight()/$dl), \Imagick::FILTER_LANCZOS, 1);
//                }
            }        
    }
    public static function prepareImg($path,$maxWidth,$maxHeight,&$wh=null){
        $tmp=  pathinfo($path);
        $dst=$tmp['dirname'].'/'.$tmp['filename'].'_preview.jpg';
        if (file_exists($dst)){
            $chk=@filemtime($dst) < @filemtime($path);
        }else{
            $chk=true;
        }
        
        $img = new Imagick( $path );
        if ($wh!==null){
            $wh=[];
            $wh['width']=$img->getimagewidth();
            $wh['height']=$img->getimageheight();
        }
        if ($chk){
            $img->setCompression(\Imagick::COMPRESSION_JPEG);
            $img->setCompressionQuality(80);
            $img->setImageFormat('jpeg');
            //$img->borderImage('black', 2,2);
            self::checkSz($img, $maxWidth, $maxHeight);
            $img->writeImage($dst);
        }
        $img->clear();
        $img->destroy();
        return \yii\helpers\StringHelper::dirname(\yii::$app->assetManager->getPublishedUrl($path)).'/'.$tmp['filename'].'_preview.jpg';
    }
    public static function convertPDF($path,$maxWidth,$maxHeight,&$wh=null){
        //return $path;
        $tmp=  pathinfo($path);
        $dst=$tmp['dirname'].'/'.$tmp['filename'].'_preview.jpg';
        if (file_exists($dst)){
            $chk=@filemtime($dst) < @filemtime($path);
        }else{
            $chk=true;
        }
        $obPdf = new \Imagick( $path.'[0]' ); #Открываем наш PDF и указываем обработчику на первую страницу
        if ($wh!==null){
            $wh=[];
            $wh['width']=$obPdf->getimagewidth();
            $wh['height']=$obPdf->getimageheight();
        }
        if ($chk){
            //$obPdf->setImageColorspace(\Imagick::COLORSPACE_); #устанавливаем цветовую палитру
            $obPdf->setCompression(\Imagick::COMPRESSION_JPEG); #Устанавливаем компрессор
            $obPdf->setCompressionQuality(60); #И уровень сжатия
            $obPdf->setImageFormat('jpeg'); #С форматом не заморачиваемся — пусть будет JPEG.
            #При необходимости сделать превью ресайзим изображение
            //$obPdf->resizeImage(250, 250, \Imagick::FILTER_LANCZOS, 1);
            #Ну и конечно же пишем в jpg-файл.
            //$obPdf->borderImage('black',2,2);
            //self::checkSz($obPdf, $maxWidth, $maxHeight);
            $obPdf->writeImage($dst);
        }
        $obPdf->clear();
        $obPdf->destroy();
        return \yii\helpers\StringHelper::dirname(\yii::$app->assetManager->getPublishedUrl($path)).'/'.$tmp['filename'].'_preview.jpg';
    }
}
