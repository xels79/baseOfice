<?php

namespace app\models;


/**
 * This is the model class for table "orders_names".
 *
 * @property integer $id
 * @property string $name
 */
class PaperName extends OneFieldTable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paperName';
    }
    public static function findName($nId){
        if ($model=self::findOne($nId)){
            return $model->name;
        } else {
            return false;
        }
    }
}
