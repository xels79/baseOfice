<?php

namespace app\models;


/**
 * This is the model class for table "orders_names".
 *
 * @property integer $id
 * @property string $name
 */
class Type_of_glue extends OneFieldTable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'type_of_glue';
    }

}
