<?php

namespace app\models;


/**
 * This is the model class for table "method_of_execution".
 *
 * @property integer $id
 * @property string $name
 */
class MethodOfExecution extends OneFieldTable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'method_of_execution';
    }

}
