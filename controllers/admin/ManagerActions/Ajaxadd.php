<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\admin\ManagerActions;
use app\models\Manager;
/**
 * Description of Ajaxadd
 *
 * @author Александер
 */
class Ajaxadd extends AjaxEdit{
    public function run(){
        $this->model=new Manager();
        $this->model->fone='{}';
        return parent::run();
    }
    
}
