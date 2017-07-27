<?php
namespace app\models;
use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
   public $authKey;      //????
   public $accessToken;  //????
        public function getName(){return $this->username;}
    	public static function tableName()
	{  
		return 'tbl_user';
	}
        public function getRole(){
           switch($this->utype){
              case 1:
                 $rVal='admin';
              break;
              case 2:
                 $rVal='moder';
              break;
              case 3:
                $rVal='logist';
              break;
              case 4:
                $rVal='bugalter';
              break;
              case 5:
                $rVal='desiner';
              break;
              case 6:
                $rVal='proizvodstvo';
              break;
              default:
                 $rVal='guest';
           }
           return $rVal;
        }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        if ($md=self::findOne($id)) 
           return new static($md);
        else
           return false;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
      /*
      * проверка токена и возврат
      * return new static($user);
      * пока не использ.
      */ 

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {       
        if(  $md=User::findOne(['username' => $username])){
           return new static( $md);
           }
        else
           return null;
           
    }    

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc   чё за фигня хз.!!!
     */
     
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc то-же хз!!!
     */
     
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
 	public function validatePassword($password)
	{
		return Yii::$app->getSecurity()->validatePassword($password,$this->password);
	}
	public function hashPassword($password)
	{
                return Yii::$app->security->generatePasswordHash($password);
		return CPasswordHelper::hashPassword($password);
	}

}
