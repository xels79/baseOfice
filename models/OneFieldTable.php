<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
/**
 * Description of OneFieldTable
 *
 * @author Александер
 */
class OneFieldTable extends \yii\db\ActiveRecord{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','toproduct'], 'required'],
            [['name','toproduct'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'name' => 'Название',
        ];
    }

}
