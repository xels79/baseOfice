<?php

namespace app\models;

use Yii;
use yii\helpers\Json;
/**
 * This is the model class for table "materials_opt".
 *
 * @property integer $id
 * @property string $name
 * @property string $options
 * @property string $rem
 */
class MaterialsOpt extends \yii\db\ActiveRecord
{
    private $tmpOpt=false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materials_opt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'options', 'rem'], 'required'],
            [['name', 'options', 'rem'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'options' => 'Options',
            'rem' => 'Rem',
        ];
    }
    public function getOptions(){
        if ($this->options){
            return Json::decode($this->options);
        }else
            return [];
    }
    public function optionByName($oName){
        if ($this->tmpOpt){
            $this->tmpOpt=$this->getOptions();
        }
        if (isset($this->tmpOpt[$oName]))
            return $this->tmpOpt[$oName];
        else
            return null;
    }
}
