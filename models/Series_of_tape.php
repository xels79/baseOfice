<?php

namespace app\models;


/**
 * This is the model class for table "orders_names".
 *
 * @property integer $id
 * @property string $name
 */
class Series_of_tape extends OneFieldTable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'series_of_tape';
    }

}
