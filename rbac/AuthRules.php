<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\rbac;
use Yii;
use \yii\base\Model;
/**
 * Description of authRules
 *
 * @author Александр
 */
class AuthRules extends \yii\base\Model{
    //put your code here
    public function run(){
        return[
//            'guest'=>[
//                'permissions'=>['login','logout','index','view','error'],
//            ],
//            'user'=>[
//                'permissions'=>['update','about'],
//                'childrens'=>['guest'],
//            ],
//            'moder'=>[
//                'permissions'=>['contact'],
//                'childrens'=>['user'],
//            ],
//            'admin'=>[
//                'permissions'=>['delete'],
//                'childrens'=>['moder']
//            ]
        ];
    }
}
