<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'baseAsterion',
    'name'=>'Астерион база',
    'version'=>'1.502a',
    'language'=>'ru-RU',
    'timeZone'=>'Europe/Moscow',
    'aliases'=>[
        '@file'=>'@app/../../baseFiles'
    ],
    
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'session'=>['name'=>'asterionBase01'],
    'assetManager' => [
        'linkAssets'=>true,
    ],
    'i18n' => [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'fileMap' => [
                    'app'       => 'app.php',
                    'app/error' => 'error.php',
                ],
            ],
        ],
    ],

        'formatter' => [
            'dateFormat'=>'php:d.m.Y',
            'locale'=>'ru-Ru'
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['admin','moder','desiner','proizvodstvo','logist','bugalter','guest'], // Здесь нет роли "guest", т.к. эта роль виртуальная и не присутствует в модели UserExt
        ],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '1111bf83tH9b7QokzW9c7hA7ZbHgQsZhcPzZ',
        ],
        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\caching\MemCache',
            'useMemcached'=>$_SERVER['SERVER_SOFTWARE']!=='Apache/2.4.10 (Win64) PHP/5.6.29',
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 60,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            //'enableAutoLogin' => true,
            'authTimeout'=>1200,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',
                'username' => 'zakaz@asterionspb.ru',
                'password' => '123456',
                'port' =>'465',// '587',
                'encryption' => 'SSL',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'response'=>'app\components\MResponse',
        'db' => require(__DIR__ . '/db.php'),
    ],
    'controllerMap'=>[
        //'viewPath'=>'app\views\admin',
        'firms'=>'app\controllers\admin\FirmsController'
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
      'class' => 'yii\debug\Module',//'yiidebugModule',
      //'allowedIPs' => ['127.0.0.1', '::1','192.168.1.*','91.122.64.106'],
        'allowedIPs'=>['*.*.*.*'],
    ];
 //   $config['modules']['debug']='yii\debug\Module';
    
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] =[
    'class' => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1', '192.168.1.*','91.122.64.106'] // adjust this to your needs
]; //'yii\gii\Module';
}

return $config;
