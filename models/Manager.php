<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "manager".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $middle_name
 * @property string $fone
 * @property string $email
 * @property integer $firm_id
 *
 * @property Firms $firm
 */
class Manager extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'firm_id'], 'required','message'=>'Поле должно быть заполнено.'],
            [['name', 'surname', 'middle_name'], 'string'],
            [['firm_id'], 'integer'],
            [['fone'], 'string', 'max' => 1024],
            [['email'], 'string', 'max' => 128]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'middle_name' => 'Отчество',
            'fone' => 'Телефон',
            'email' => 'Email',
            'firm_id' => 'Фирма',
            'firmName'=> 'Фирма'
        ];
    }
    public function getFoneCount(){
        $tmp=\yii\helpers\Json::decode($this->fone);
        return count($tmp);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFirms()
    {
        return $this->hasOne(Firms::className(), ['id' => 'firm_id']);
    }
    public function getFirmName()
    {
        if ($this->firm_id)
            return $this->hasOne(Firms::className(), ['id' => 'firm_id'])->one()->name;
        else
            return '';
    }
    public function getFullName(){
        $middle_name=ArrayHelper::getValue($this,'middle_name',null);
        $surname=ArrayHelper::getValue($this,'surname',null);
        $rVal=$this->name;
        if ($middle_name) $rVal.=' '.$middle_name;
        if ($surname)$rVal.=' '.$surname;
        return $rVal;
    }
    public static function createDetailAttr($model){
        $middle_name=ArrayHelper::getValue($model,'middle_name',null);
        $surname=ArrayHelper::getValue($model,'surname',null);
        $email=ArrayHelper::getValue($model,'email',null);
        $fone=\yii\helpers\Json::decode(ArrayHelper::getValue($model,'fone','{}'));
        $itmes=['name:ntext:Имя'];
        if ($middle_name!=null) $itmes[]='middle_name:ntext:Отчество';
        if ($surname!=null) $itmes[]='surname:ntext:Фамилия';
        if ($email!=null) $itmes[]='email:email';
        if (count($fone)>0){
            $itmes[]=[
                    'label'=>'Телефоны',
                    'format'=>'raw',
                    'value'=>\app\widgets\ActiveFones::widget([
                        'model'=>$model,
                        'attribute'=>'fone',
                        'buttonAddOptions'=>['class'=>'btn btn-success','style'=>'margin:3px 5px;'],
                        'editable'=>false,
                        'panel'=>false,
                        'short'=>true
                        ])
            ];
        }
        return $itmes;
    }
    
    public function getDetailAttr(){
        return self::createDetailAttr($this);
    }

}
