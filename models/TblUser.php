<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property integer $utype
 * @property string $realname
 */
class TblUser extends \yii\db\ActiveRecord
{
    public $npassword=false;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['utype'], 'integer'],
            [['username', 'password', 'email', 'realname'], 'string', 'max' => 128]
        ];
    }
    public function getUtypeRus(){
        switch ($this->utype){
            case 1:
                return 'Администратор';
            case 2:
                return 'Модератор';
            case 3:
                return 'Логистика';
            case 4:
                return 'Бугалтер';
            case 5:
                return 'Дизайнер';
            case 6:
                return 'Производство';
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор',
            'username' => 'Имя пользователя',
            'npassword' => 'Пароль',
            'email' => 'Почта',
            'utype' => 'Идентификатор типа',
            'utypeRus'=>'Уровень доступа',
            'realname' => 'Менеджер',
        ];
    }
}
