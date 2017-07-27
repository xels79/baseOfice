<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.less',
        'css/upload.less'
    ];
    public $js = [
        'js/baseFunction.js',
        'js/asterion.js',
        'js/mProgress.js',
        'js/uloader.js'
    ];
    public $depends = [
//        //\yii\bootstrap\BootstrapAsset::className(),
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'yii\web\YiiAsset',

 
    ];
    public $jsOptions=[
        'position'=>yii\web\View::POS_END
    ];
}
